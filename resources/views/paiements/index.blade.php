
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des paiements</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des paiements</h1>
        <a href="{{ route('factures.index') }}" class="btn btn-secondary">
            Retour aux factures
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Facture</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Référence</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($paiements as $paiement)
                        <tr>
                            <td>{{ $paiement->id }}</td>

                            <td>
                                {{ $paiement->facture->numero ?? 'Facture inconnue' }}
                            </td>

                            <td>
                                {{ $paiement->facture->client->nom ?? 'Client inconnu' }}
                            </td>

                            <td>
                                @switch($paiement->type)
                                    @case('especes')
                                        Espèces
                                        @break
                                    @case('cheque')
                                        Chèque
                                        @break
                                    @case('cambiale')
                                        Cambiale
                                        @break
                                    @default
                                        {{ $paiement->type }}
                                @endswitch
                            </td>

                            <td>
                                {{ number_format($paiement->montant, 3, ',', ' ') }} TND
                            </td>

                            <td>
                                {{ $paiement->date_paiement
                                    ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y')
                                    : '-' }}
                            </td>

                            <td>{{ $paiement->reference ?? '-' }}</td>

                            <td>
                                @php
                                    $couleurs = [
                                        'paye' => 'success',
                                        'a_traiter' => 'warning',
                                        'depose' => 'info',
                                        'encaisse' => 'success',
                                        'rejete' => 'danger',
                                    ];

                                    $libelles = [
                                        'paye' => 'Payé',
                                        'a_traiter' => 'À traiter',
                                        'depose' => 'Déposé',
                                        'encaisse' => 'Encaissé',
                                        'rejete' => 'Rejeté',
                                    ];
                                @endphp

                                <span class="badge bg-{{ $couleurs[$paiement->statut] ?? 'secondary' }}">
                                    {{ $libelles[$paiement->statut] ?? $paiement->statut }}
                                </span>
                            </td>

                            <td>
                                @if($paiement->facture)
                                    <a
                                        href="{{ route('factures.paiements.create', $paiement->facture->id) }}"
                                        class="btn btn-sm btn-success"
                                    >
                                        Ajouter un paiement
                                    </a>
                                @else
                                    <span class="text-muted">Facture indisponible</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                Aucun paiement enregistré.
                                <br>
                                Pour commencer, ajoute un paiement depuis la liste des factures.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
