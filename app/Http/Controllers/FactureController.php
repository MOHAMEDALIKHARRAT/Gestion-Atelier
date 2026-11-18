<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\Piece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nette\Schema\ValidationException;

class FactureController extends Controller
{
    private const TAUX_TVA = 0.19;
    private const TIMBRE_FISCAL = 1.000;

    /**
     * Afficher la liste des factures.
     */
    public function index()
    {
        $factures = Facture::with('client')
            ->latest()
            ->get();

        return view('factures.index', compact('factures'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        $clients = Client::orderBy('nom')->get();
        $pieces = Piece::orderBy('nom')->get();

        return view('factures.create', compact('clients', 'pieces'));
    }

    /**
     * Enregistrer une nouvelle facture.
     */
    public function store(Request $request)
    {
        $donnees = $request->validate([
            'client_id' => [
                'required',
                'exists:clients,id',
            ],

            'date_facture' => [
                'required',
                'date',
            ],

            'remise' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'lignes' => [
                'required',
                'array',
                'min:1',
            ],

            'lignes.*.type' => [
                'required',
                'in:piece,main_oeuvre,deplacement,autre',
            ],

            'lignes.*.piece_id' => [
                'nullable',
                'required_if:lignes.*.type,piece',
                'exists:pieces,id',
            ],

            'lignes.*.designation' => [
                'required',
                'string',
                'max:255',
            ],

            'lignes.*.quantite' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'lignes.*.prix_unitaire_ht' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $facture = DB::transaction(function () use ($donnees) {

            /*
             * 1. Regrouper les quantités demandées par pièce.
             */
            $quantitesParPiece = [];

            foreach ($donnees['lignes'] as $ligne) {
                if ($ligne['type'] === 'piece') {
                    $id = (int) $ligne['piece_id'];

                    $quantitesParPiece[$id] =
                        ($quantitesParPiece[$id] ?? 0)
                        + (float) $ligne['quantite'];
                }
            }

            /*
             * 2. Vérifier le stock et verrouiller les pièces.
             */
            $pieces = Piece::whereIn(
                'id',
                array_keys($quantitesParPiece)
            )
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($quantitesParPiece as $id => $quantiteDemandee) {
                if (!isset($pieces[$id])) {
                    throw ValidationException::withMessages([
                        'lignes' => "Une pièce sélectionnée n'existe plus.",
                    ]);
                }

                if ((float) $pieces[$id]->quantite < $quantiteDemandee) {
                    throw ValidationException::withMessages([
                        'lignes' => "Stock insuffisant pour « "
                            . $pieces[$id]->nom
                            . " » (disponible : "
                            . $pieces[$id]->quantite
                            . ", demandé : "
                            . $quantiteDemandee
                            . ").",
                    ]);
                }
            }

            /*
             * 3. Calculer le total HT des lignes.
             */
            $totalHt = 0;
            $lignesCalculees = [];

            foreach ($donnees['lignes'] as $ligne) {
                $quantite = (float) $ligne['quantite'];
                $prixUnitaire = (float) $ligne['prix_unitaire_ht'];

                $totalLigne = round(
                    $quantite * $prixUnitaire,
                    3
                );

                $totalHt += $totalLigne;

                $lignesCalculees[] = [
                    'type' => $ligne['type'],

                    'piece_id' => $ligne['type'] === 'piece'
                        ? (int) $ligne['piece_id']
                        : null,

                    'designation' => $ligne['designation'],
                    'quantite' => $quantite,
                    'prix_unitaire_ht' => round($prixUnitaire, 3),

                    // Nom du champ prévu dans la migration.
                    'total_ligne_ht' => $totalLigne,
                ];
            }

            $totalHt = round($totalHt, 3);

            /*
             * 4. Calculer la remise, la TVA et le total TTC.
             */
            $remise = min(
                (float) ($donnees['remise'] ?? 0),
                $totalHt
            );

            $remise = round($remise, 3);

            $baseTva = round($totalHt - $remise, 3);

            $tva = round(
                $baseTva * self::TAUX_TVA,
                3
            );

            $timbre = self::TIMBRE_FISCAL;

            $totalTtc = round(
                $baseTva + $tva + $timbre,
                3
            );

            /*
             * 5. Générer un numéro de facture unique.
             */
            $numero = 'FAC-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(bin2hex(random_bytes(3)));

            /*
             * 6. Créer la facture.
             *
             * Ce code utilise le champ "tva".
             * Si ta migration utilise "total_tva", adapte ce nom.
             */
            $facture = Facture::create([
                'numero' => $numero,
                'client_id' => $donnees['client_id'],
                'date_facture' => $donnees['date_facture'],
                'total_ht' => $totalHt,
                'remise' => $remise,
                'tva' => $tva,
                'timbre' => $timbre,
                'total_ttc' => $totalTtc,
                'montant_paye' => 0,
                'reste_a_payer' => $totalTtc,
                'statut_paiement' => 'impayee',
            ]);

            /*
             * 7. Enregistrer les lignes de facture.
             */
            $facture->lignes()->createMany($lignesCalculees);

            /*
             * 8. Déduire du stock les pièces vendues.
             */
            foreach ($quantitesParPiece as $id => $quantite) {
                Piece::where('id', $id)
                    ->decrement('quantite', $quantite);
            }

            return $facture;
        });

        return redirect()
            ->route('factures.index')
            ->with(
                'success',
                "La facture {$facture->numero} a été créée avec succès."
            );
    }

    /**
     * Afficher une facture.
     */
    public function show(Facture $facture)
    {
        $facture->load([
            'client',
            'lignes.piece',
        ]);

        return view('factures.show', compact('facture'));
    }

    /**
     * Modification désactivée pour éviter les erreurs de stock.
     */
    public function edit(Facture $facture)
    {
        return redirect()
            ->route('factures.show', $facture)
            ->with(
                'info',
                'La modification des factures n’est pas encore activée.'
            );
    }

    public function update(Request $request, Facture $facture)
    {
        return redirect()
            ->route('factures.show', $facture)
            ->with(
                'info',
                'La modification des factures n’est pas encore activée.'
            );
    }

    /**
     * Suppression désactivée pour préserver l'historique du stock.
     */
    public function destroy(Facture $facture)
    {
        return redirect()
            ->route('factures.index')
            ->with(
                'error',
                'La suppression des factures est désactivée pour préserver le stock.'
            );
    }
}
