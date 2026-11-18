<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Piece;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Chiffre d'affaires du mois
        $chiffreAffairesMois = Facture::whereMonth('date_facture', now()->month)
            ->whereYear('date_facture', now()->year)
            ->sum('total_ttc');

        // Nombre de factures
        $nombreFactures = Facture::count();

        // Factures impayees
        $facturesImpayees = Facture::where('reste_a_payer', '>', 0)->count();

        // Pieces avec stock faible
        $piecesEnAlerte = Piece::whereColumn(
            'quantite',
            '<=',
            'seuil_alerte'
        )->count();

        // Paiements en attente
        $paiements = Paiement::with('facture')
            ->whereIn('type', ['cheque', 'cambiale'])
            ->whereIn('statut', ['a_traiter', 'depose'])
            ->get();

        // Date de paiement
        $paiements = $paiements->map(function ($paiement) {
            if ($paiement->type === 'cheque') {
                $date = $paiement->date_depot_prevue;
            } else {
                $date = $paiement->date_echeance;
            }

            $paiement->date_a_payer = $date
                ? Carbon::parse($date)
                : null;

            return $paiement;
        })->filter(function ($paiement) {
            return $paiement->date_a_payer !== null;
        });

        // Echeances aujourd'hui
        $echeancesAujourdhui = $paiements->filter(function ($paiement) {
            return $paiement->date_a_payer->isToday();
        })->count();

        // Paiements en retard
        $enRetard = $paiements->filter(function ($paiement) {
            return $paiement->date_a_payer->lt(today());
        });

        // Prochaines echeances
        $prochainesEcheances = $paiements
            ->filter(function ($paiement) {
                return $paiement->date_a_payer->gte(today());
            })
            ->sortBy('date_a_payer')
            ->take(10);

        return view('dashboard.index', compact(
            'chiffreAffairesMois',
            'nombreFactures',
            'facturesImpayees',
            'piecesEnAlerte',
            'echeancesAujourdhui',
            'enRetard',
            'prochainesEcheances'
        ));
    }
}
