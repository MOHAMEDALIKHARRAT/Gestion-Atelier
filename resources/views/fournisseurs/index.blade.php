@extends('layouts.app')

@section('title', 'Fournisseurs')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Gestion des fournisseurs</h2>

    <a href="{{ route('fournisseurs.create') }}" class="btn btn-success">
        + Ajouter un fournisseur
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">

        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Adresse</th>
                    <th>Matricule fiscal</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($fournisseurs as $fournisseur)
                    <tr>
                        <td>{{ $fournisseur->nom }}</td>
                        <td>{{ $fournisseur->telephone ?? '-' }}</td>
                        <td>{{ $fournisseur->email ?? '-' }}</td>
                        <td>{{ $fournisseur->adresse ?? '-' }}</td>
                        <td>{{ $fournisseur->matricule_fiscal ?? '-' }}</td>

                        <td class="text-nowrap">
                            <a
                                href="{{ route('fournisseurs.edit', $fournisseur) }}"
                                class="btn btn-warning btn-sm"
                            >
                                Modifier
                            </a>

                            <form
                                action="{{ route('fournisseurs.destroy', $fournisseur) }}"
                                method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Supprimer ce fournisseur ?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            Aucun fournisseur enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection