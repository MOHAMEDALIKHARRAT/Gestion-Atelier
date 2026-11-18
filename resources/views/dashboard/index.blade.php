@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Dashboard</h1>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card p-3">
                <h6>Monthly revenue</h6>
                <h3>{{ number_format($chiffreAffairesMois, 2) }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Total invoices</h6>
                <h3>{{ $nombreFactures }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Unpaid invoices</h6>
                <h3>{{ $facturesImpayees }}</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Low stock items</h6>
                <h3>{{ $piecesEnAlerte }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h5>Payments due today</h5>
                <h3>{{ $echeancesAujourdhui }}</h3>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3">
                <h5>Late payments</h5>
                <h3>{{ $enRetard->count() }}</h3>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-4">
        <h5>Upcoming payments</h5>

        @if($prochainesEcheances->isEmpty())
            <p>No upcoming payments.</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Due date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prochainesEcheances as $paiement)
                            <tr>
                                <td>{{ $paiement->facture->numero ?? '-' }}</td>
                                <td>{{ ucfirst($paiement->type) }}</td>
                                <td>{{ $paiement->reference ?? '-' }}</td>
                                <td>{{ number_format($paiement->montant, 2) }}</td>
                                <td>
                                    {{ $paiement->date_a_payer
                                        ? $paiement->date_a_payer->format('d/m/Y')
                                        : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection