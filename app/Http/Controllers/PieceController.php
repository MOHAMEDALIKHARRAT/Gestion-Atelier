<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\Piece;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PieceController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $recherche = $request->input('recherche');
        $pieces = Piece::with('fournisseur')
            ->when($recherche, function ($query, $recherche) {
                $query->where(function ($q) use ($recherche) {
                    $q->where('nom', 'like', '%' . $recherche . '%')
                      ->orWhere('reference', 'like', '%' . $recherche . '%');
                });
            })
            ->latest()->paginate(10)->withQueryString();

        return view('pieces.index', compact('pieces', 'recherche'));
    }

    public function create()
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('pieces.create', compact('fournisseurs'));
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'reference' => [
                'required', 'string', 'max:255', 'unique:pieces,reference'
            ],
            'quantite' => ['required', 'integer', 'min:0'],
            'prix_achat' => ['required', 'numeric', 'min:0'],
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['required', 'integer', 'min:0'],
            'fournisseur_id' => [
                'nullable', 'exists:fournisseurs,id'
            ],
        ]);

        Piece::create($donnees);

        return redirect()
            ->route('pieces.index')
            ->with('success', 'Pièce ajoutée avec succès.');
    }

    public function edit(Piece $piece)
    {
        $fournisseurs = Fournisseur::orderBy('nom')->get();

        return view('pieces.edit', compact('piece', 'fournisseurs'));
    }

    public function update(Request $request, Piece $piece)
    {
        $donnees = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'reference' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pieces', 'reference')->ignore($piece->id),
            ],
            'quantite' => ['required', 'integer', 'min:0'],
            'prix_achat' => ['required', 'numeric', 'min:0'],
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['required', 'integer', 'min:0'],
            'fournisseur_id' => [
                'nullable', 'exists:fournisseurs,id'
            ],
        ]);

        $piece->update($donnees);

        return redirect()
            ->route('pieces.index')
            ->with('success', 'Pièce modifiée avec succès.');
    }

    public function destroy(Piece $piece)
    {
        $piece->delete();

        return redirect()
            ->route('pieces.index')
            ->with('success', 'Pièce supprimée avec succès.');
    }
}
