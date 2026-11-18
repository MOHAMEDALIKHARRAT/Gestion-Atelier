
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Gestion des employés</h2>
            <p class="text-muted">Liste et informations du personnel</p>
        </div>

        <a href="{{ route('employes.create') }}" class="btn btn-primary">
            + Ajouter un employé
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Employé</th>
                            <th>CIN</th>
                            <th>Téléphone</th>
                            <th>Poste</th>
                            <th>Salaire mensuel</th>
                            <th>Horaires</th>
                            <th>Carte d'identité</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($employes as $employe)
                            <tr>
                                <td>
                                    <strong>{{ $employe->user->name }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $employe->user->email }}
                                    </small>
                                </td>

                                <td>{{ $employe->cin }}</td>

                                <td>{{ $employe->telephone ?? '-' }}</td>

                                <td>{{ $employe->poste ?? '-' }}</td>

                                <td>
                                    <strong>
                                        {{ number_format($employe->salaire_mensuel, 3) }}
                                        TND
                                    </strong>
                                </td>

                                <td>
                                    {{ substr($employe->heure_debut, 0, 5) }}
                                    -
                                    {{ substr($employe->heure_fin, 0, 5) }}
                                </td>

                                <td>
                                    @if($employe->photo_cin)
                                        <a href="{{ asset('storage/' . $employe->photo_cin) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary">
                                            Voir la CIN
                                        </a>
                                    @else
                                        <span class="text-muted">Non fournie</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    Aucun employé enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 text-muted">
        Nombre total d'employés : <strong>{{ $employes->count() }}</strong>
    </div>

</div>
@endsection
