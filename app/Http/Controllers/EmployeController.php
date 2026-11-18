<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeController extends Controller
{  public function index()
    {
        $employes = Employe::with('user')->get();

        return view('employes.index', compact('employes'));
    }

    public function create()
    {
        return view('employes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',

            'cin' => 'required|string|max:20|unique:employes,cin',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'photo_cin' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'poste' => 'nullable|string|max:100',
            'date_embauche' => 'nullable|date',
            'salaire_mensuel' => 'required|numeric|min:0',
            'heure_debut' => 'required',
            'heure_fin' => 'required',
            'pause_minutes' => 'required|numeric|min:0',
            'tolerance_retard' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {

            // Création du compte utilisateur
            $utilisateur = new User();

            $utilisateur->name = $request->nom . ' ' . $request->prenom;
            $utilisateur->email = $request->email;
            $utilisateur->password = Hash::make($request->password);
            $utilisateur->role = 'employer';

            $utilisateur->save();

            // Enregistrement de la photo de la CIN
            $photo = null;

            if ($request->hasFile('photo_cin')) {
                $photo = $request->file('photo_cin')
                    ->store('cartes-identite', 'public');
            }

            // Création de la fiche employé
            Employe::create([
                'user_id' => $utilisateur->id,
                'cin' => $request->cin,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
                'photo_cin' => $photo,
                'poste' => $request->poste,
                'date_embauche' => $request->date_embauche,
                'salaire_mensuel' => $request->salaire_mensuel,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => $request->heure_fin,
                'pause_minutes' => $request->pause_minutes,
                'tolerance_retard' => $request->tolerance_retard,
            ]);
        });

        return redirect()->route('employes.index')
            ->with('success', 'Employé et compte créés avec succès.');
    }
}