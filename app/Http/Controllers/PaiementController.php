<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaiementController extends Controller
{
   public function index()
    {
        $paiements = Paiement::with('facture.client')
            ->latest()
            ->get();

        return view('paiements.index', compact('paiements'));
    }

    public function create(Facture $facture)
    {
        $facture->load(['client', 'paiements']);

        return view('paiements.create', compact('facture'));
    }

    public function store(Request $request, Facture $facture)
    {
        $donnees = $request->validate([
            'type' => [
                'required',
                Rule::in(['especes', 'cheque', 'cambiale']),
            ],
            'montant' => ['required', 'numeric', 'gt:0'],
            'date_paiement' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:255'],
            'banque' => ['nullable', 'string', 'max:255'],
            'titulaire' => ['nullable', 'string', 'max:255'],
            'date_cheque' => ['nullable', 'required_if:type,cheque', 'date'],
            'date_depot_prevue' => ['nullable', 'required_if:type,cheque', 'date'],
            'date_echeance' => ['nullable', 'required_if:type,cambiale', 'date'],
            'observation' => ['nullable', 'string'],
        ]);

        $paiement = DB::transaction(function () use ($donnees, $facture) {
            $factureVerrouillee = Facture::whereKey($facture->id)
                ->lockForUpdate()
                ->firstOrFail();

            $reste = (float) $factureVerrouillee->reste_a_payer;

            if ((float) $donnees['montant'] > $reste + 0.0001) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'montant' => 'Le montant dépasse le reste à payer de '
                        . number_format($reste, 3, '.', '')
                        . ' TND.',
                ]);
            }

            $type = $donnees['type'];

            $statut = $type === 'especes' ? 'paye' : 'a_traiter';

            $paiement = $factureVerrouillee->paiements()->create([
                'type' => $type,
                'montant' => round((float) $donnees['montant'], 3),
                'date_paiement' => $donnees['date_paiement'],
                'reference' => $donnees['reference'] ?? null,
                'banque' => $donnees['banque'] ?? null,
                'titulaire' => $donnees['titulaire'] ?? null,
                'date_cheque' => $donnees['date_cheque'] ?? null,
                'date_depot_prevue' => $type === 'cheque'
                    ? ($donnees['date_depot_prevue'] ?? null)
                    : null,
                'date_echeance' => $type === 'cambiale'
                    ? ($donnees['date_echeance'] ?? null)
                    : null,
                'statut' => $statut,
                'observation' => $donnees['observation'] ?? null,
            ]);

            $this->recalculerFacture($factureVerrouillee);

            return $paiement;
        });

        return redirect()
            ->route('paiements.index')
            ->with('success', 'Paiement enregistré avec succès.');
    }

    public function updateStatut(Request $request, Paiement $paiement)
    {
        $donnees = $request->validate([
            'statut' => [
                'required',
                Rule::in(['a_traiter', 'depose', 'encaisse', 'rejete']),
            ],
            'date_depot_effectif' => ['nullable', 'date'],
            'date_encaissement' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($donnees, $paiement) {
            $paiement = Paiement::whereKey($paiement->id)
                ->lockForUpdate()
                ->firstOrFail();

            $paiement->statut = $donnees['statut'];

            if ($paiement->type === 'cheque'
                && $donnees['statut'] === 'depose') {
                $paiement->date_depot_effectif =
                    $donnees['date_depot_effectif'] ?? now()->toDateString();
            }

            if ($donnees['statut'] === 'encaisse') {
                $paiement->date_encaissement =
                    $donnees['date_encaissement'] ?? now()->toDateString();
            }

            $paiement->save();

            $facture = Facture::whereKey($paiement->facture_id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->recalculerFacture($facture);
        });

        return redirect()
            ->route('paiements.index')
            ->with('success', 'Statut du paiement mis à jour.');
    }

    private function recalculerFacture(Facture $facture): void
    {
        // Un paiement rejeté ne compte plus dans le règlement de la facture.
        // Un chèque/cambiale en attente compte comme règlement remis,
        // mais reste à suivre jusqu'à son encaissement.
        $montantPaye = (float) $facture->paiements()
            ->where('statut', '!=', 'rejete')
            ->sum('montant');

        $totalTtc = (float) $facture->total_ttc;
        $reste = max(0, round($totalTtc - $montantPaye, 3));

        $facture->montant_paye = round($montantPaye, 3);
        $facture->reste_a_payer = $reste;

        if ($reste <= 0.0001) {
            $facture->statut_paiement = 'payee';
        } elseif ($montantPaye > 0) {
            $facture->statut_paiement = 'partielle';
        } else {
            $facture->statut_paiement = 'impayee';
        }

        $facture->save();
    }
}
