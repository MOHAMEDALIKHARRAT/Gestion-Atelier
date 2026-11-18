<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Pointage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PointageController extends Controller
{
   // Afficher les pointages du jour
    public function index()
    {
        $date = Carbon::today();

        $employes = Employe::with('user')->get();

        $pointages = Pointage::with('employe.user')
            ->whereDate('date_pointage', $date)
            ->get();

        $totalEmployes = $employes->count();

        $totalPresents = $pointages
            ->where('statut', 'present')
            ->count();

        $totalRetards = $pointages
            ->where('retard_minutes', '>', 0)
            ->count();

        return view('pointages.index', compact(
            'date',
            'employes',
            'pointages',
            'totalEmployes',
            'totalPresents',
            'totalRetards'
        ));
    }

    // Enregistrer l'arrivée d'un employé
    public function arriver(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
        ]);

        $employe = Employe::findOrFail($request->employe_id);

        $date = Carbon::today();

        $pointage = Pointage::firstOrNew([
            'employe_id' => $employe->id,
            'date_pointage' => $date,
        ]);

        // Vérifier si l'arrivée est déjà enregistrée
        if ($pointage->heure_arrivee) {
            return back()->with('error', 'L’arrivée est déjà enregistrée.');
        }

        // Enregistrer l'heure réelle
        $heureArrivee = Carbon::now();

        $pointage->heure_arrivee = $heureArrivee->format('H:i:s');
        $pointage->statut = 'present';

        // Récupérer l'heure de début prévue
        $heureDebut = Carbon::today()->setTimeFromTimeString(
            $employe->heure_debut
        );

        // Calculer le retard exact en minutes
        $difference = $heureArrivee->timestamp - $heureDebut->timestamp;

        $retard = max(0, (int) floor($difference / 60));

        $pointage->retard_minutes = $retard;

        $pointage->save();

        if ($retard > 0) {
            return back()->with(
                'success',
                "Arrivée enregistrée. Retard de $retard minute(s)."
            );
        }

        return back()->with(
            'success',
            'Arrivée enregistrée. Employé à l’heure !'
        );
    }

    // Enregistrer le départ d'un employé
    public function partir(Request $request)
    {
        $request->validate([
            'employe_id' => 'required|exists:employes,id',
        ]);

        $employe = Employe::findOrFail($request->employe_id);

        $date = Carbon::today();

        $pointage = Pointage::where('employe_id', $employe->id)
            ->whereDate('date_pointage', $date)
            ->first();

        if (!$pointage || !$pointage->heure_arrivee) {
            return back()->with(
                'error',
                'Vous devez enregistrer l’arrivée avant le départ.'
            );
        }

        if ($pointage->heure_depart) {
            return back()->with(
                'error',
                'Le départ est déjà enregistré.'
            );
        }

        // Enregistrer l'heure de départ
        $heureDepart = Carbon::now();

        $pointage->heure_depart = $heureDepart->format('H:i:s');

        // Calculer la durée totale de présence
        $heureArrivee = Carbon::today()->setTimeFromTimeString(
            $pointage->heure_arrivee
        );

        $minutesTotales = max(
            0,
            (int) floor(($heureDepart->timestamp - $heureArrivee->timestamp) / 60)
        );

        // Retirer la durée de pause
        $pause = (int) $employe->pause_minutes;

        $travailMinutes = max(0, $minutesTotales - $pause);

        $pointage->travail_minutes = $travailMinutes;

        // Calculer la durée normale de travail
        $debut = Carbon::today()->setTimeFromTimeString(
            $employe->heure_debut
        );

        $fin = Carbon::today()->setTimeFromTimeString(
            $employe->heure_fin
        );

        $dureePrevue = max(
            0,
            (int) floor(($fin->timestamp - $debut->timestamp) / 60) - $pause
        );

        // Calculer les heures supplémentaires
        $supplementaires = max(0, $travailMinutes - $dureePrevue);

        $pointage->supplementaires_minutes = $supplementaires;

        $pointage->save();

        return back()->with(
            'success',
            'Départ enregistré. Temps travaillé : '
            . $travailMinutes . ' minutes. Heures supplémentaires : '
            . $supplementaires . ' minutes.'
        );
    }

// Afficher le formulaire de correction
public function edit($id)
{
    $pointage = Pointage::with('employe.user')->findOrFail($id);

    return view('pointages.edit', compact('pointage'));
}

// Enregistrer la correction du pointage
public function update(Request $request, $id)
{
    $request->validate([
        'date_pointage' => 'required|date',
        'heure_arrivee' => 'nullable|date_format:H:i',
        'heure_depart' => 'nullable|date_format:H:i',
    ]);

    $pointage = Pointage::with('employe')->findOrFail($id);
    $employe = $pointage->employe;

    $date = $request->date_pointage;
    $arrivee = $request->heure_arrivee;
    $depart = $request->heure_depart;

    // Enregistrer la date et les heures corrigées
    $pointage->date_pointage = $date;
    $pointage->heure_arrivee = $arrivee;
    $pointage->heure_depart = $depart;

    // Recalculer le retard
    $retard = 0;

    if ($arrivee) {
        $heureReelle = Carbon::parse($date . ' ' . $arrivee);
        $heurePrevue = Carbon::parse($date . ' ' . $employe->heure_debut);

        if ($heureReelle->greaterThan($heurePrevue)) {
            $retard = $heurePrevue->diffInMinutes($heureReelle);
        }
    }

    $pointage->retard_minutes = $retard;

    // Recalculer le temps de travail et les heures supplémentaires
    $travailMinutes = 0;
    $supplementaires = 0;

    if ($arrivee && $depart) {
        $debutTravail = Carbon::parse($date . ' ' . $arrivee);
        $finTravail = Carbon::parse($date . ' ' . $depart);

        // Gérer aussi un départ après minuit
        if ($finTravail->lessThan($debutTravail)) {
            $finTravail->addDay();
        }

        $minutesTotales = $debutTravail->diffInMinutes($finTravail);

        $pause = (int) $employe->pause_minutes;

        $travailMinutes = max(0, $minutesTotales - $pause);

        $debutPrevu = Carbon::parse($date . ' ' . $employe->heure_debut);
        $finPrevue = Carbon::parse($date . ' ' . $employe->heure_fin);

        if ($finPrevue->lessThan($debutPrevu)) {
            $finPrevue->addDay();
        }

        $dureePrevue = max(
            0,
            $debutPrevu->diffInMinutes($finPrevue) - $pause
        );

        $supplementaires = max(0, $travailMinutes - $dureePrevue);
    }

    $pointage->travail_minutes = $travailMinutes;
    $pointage->supplementaires_minutes = $supplementaires;

    $pointage->save();

    return redirect()
        ->route('pointages.index')
        ->with('success', 'Pointage corrigé avec succès.');
}

}
