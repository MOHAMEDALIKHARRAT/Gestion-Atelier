
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des factures</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des factures</h1>

        <a href="{{ route('factures.create') }}" class="btn btn-primary">
            + Nouvelle facture
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

    <div class="card shadow-sm">
        <div class="card-body table-responsive">

            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Numéro</th>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Total TTC</th>
                        <th>Payé</th>
                        <th>Reste à payer</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($factures as $facture)
                        <tr>
                            <td>{{ $facture->numero }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($facture->date_facture)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $facture->client->nom ?? 'Client inconnu' }}
                            </td>

                            <td>
                                {{ number_format($facture->total_ttc, 3, ',', ' ') }} TND
                            </td>

                            <td>
                                {{ number_format($facture->montant_paye, 3, ',', ' ') }} TND
                            </td>

                            <td>
                                {{ number_format($facture->reste_a_payer, 3, ',', ' ') }} TND
                            </td>

                            <td>
                                @if($facture->statut_paiement === 'payee')
                                    <span class="badge bg-success">Payée</span>
                                @elseif($facture->statut_paiement === 'partielle')
                                    <span class="badge bg-warning text-dark">Partielle</span>
                                @else
                                    <span class="badge bg-danger">Impayée</span>
                                @endif
                            </td>

                            <td class="text-nowrap">
                                <a
                                    href="{{ route('factures.show', $facture->id) }}"
                                    class="btn btn-info btn-sm"
                                >
                                    Détails
                                </a>

                                @if((float) $facture->reste_a_payer > 0)
                                    <a
                                        href="{{ route('factures.paiements.create', $facture->id) }}"
                                        class="btn btn-success btn-sm"
                                    >
                                        Ajouter paiement
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                Aucune facture enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('paiements.index') }}" class="btn btn-outline-primary">
            Consulter tous les paiements
        </a>
    </div>

</div>

</body>
</html>
