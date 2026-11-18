
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Facture {{ $facture->numero }}</h2>
        <button onclick="window.print()" class="btn btn-secondary">
            Imprimer
        </button>
    </div>

    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <h4>Informations du client</h4>
            <p>
                <strong>Client :</strong>
                {{ $facture->client->nom }} {{ $facture->client->prenom }}
            </p>
            <p><strong>Téléphone :</strong> {{ $facture->client->telephone ?? '-' }}</p>
            <p><strong>Date :</strong> {{ $facture->date_facture->format('d/m/Y') }}</p>

            <hr>

            <h4>Lignes de facture</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Désignation</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Prix unitaire HT</th>
                            <th>Total HT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facture->lignes as $ligne)
                            <tr>
                                <td>{{ $ligne->designation }}</td>
                                <td>{{ $ligne->type === 'piece' ? 'Pièce' : 'Prestation' }}</td>
                                <td>{{ $ligne->quantite }}</td>
                                <td>{{ number_format((float) $ligne->prix_unitaire_ht, 3, '.', ' ') }} TND</td>
                                <td>{{ number_format((float) $ligne->total_ligne_ht, 3, '.', ' ') }} TND</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-5">
                    <p>Sous-total HT : {{ number_format((float) $facture->total_ht, 3, '.', ' ') }} TND</p>
                    <p>Remise : {{ number_format((float) $facture->remise, 3, '.', ' ') }} TND</p>
                    <p>TVA : {{ number_format((float) $facture->tva, 3, '.', ' ') }} TND</p>
                    <p>Timbre fiscal : {{ number_format((float) $facture->timbre, 3, '.', ' ') }} TND</p>
                    <h4>Total TTC : {{ number_format((float) $facture->total_ttc, 3, '.', ' ') }} TND</h4>
                    <p>Montant payé : {{ number_format((float) $facture->montant_paye, 3, '.', ' ') }} TND</p>
                    <h5>Reste à payer : {{ number_format((float) $facture->reste_a_payer, 3, '.', ' ') }} TND</h5>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('factures.index') }}" class="btn btn-secondary mt-3">
        Retour aux factures
    </a>
</div>

<style>
@media print {
    nav, .btn, header, footer {
        display: none !important;
    }

    body {
        background: white !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
