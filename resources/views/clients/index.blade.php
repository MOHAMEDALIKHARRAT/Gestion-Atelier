
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des clients</h2>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">
            + Ajouter un client
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Matricule fiscal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr>
                            <td>{{ $client->nom }}</td>
                            <td>{{ $client->prenom ?? '-' }}</td>
                            <td>{{ $client->telephone ?? '-' }}</td>
                            <td>{{ $client->adresse ?? '-' }}</td>
                            <td>{{ $client->matricule_fiscal ?? '-' }}</td>
                            <td>
                                <a href="{{ route('clients.edit', $client) }}"
                                   class="btn btn-warning btn-sm">
                                    Modifier
                                </a>

                                <form action="{{ route('clients.destroy', $client) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Supprimer ce client ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                Aucun client enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
