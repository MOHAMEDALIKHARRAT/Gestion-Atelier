
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Enregistrer un paiement</h2>

    <div class="card mb-4">
        <div class="card-body">
            <p><strong>Facture :</strong> {{ $facture->numero }}</p>
            <p><strong>Client :</strong>
                {{ $facture->client->nom }} {{ $facture->client->prenom }}
            </p>
            <p><strong>Total TTC :</strong> {{ number_format($facture->total_ttc, 3) }} TND</p>
            <p><strong>Déjà réglé :</strong> {{ number_format($facture->montant_paye, 3) }} TND</p>
            <p><strong>Reste à payer :</strong>
                <span class="text-danger">
                    {{ number_format($facture->reste_a_payer, 3) }} TND
                </span>
            </p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('factures.paiements.store', $facture) }}">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Moyen de paiement</label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="especes">Espèces</option>
                        <option value="cheque" @selected(old('type') === 'cheque')>Chèque</option>
                        <option value="cambiale" @selected(old('type') === 'cambiale')>Cambiale</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Montant (TND)</label>
                    <input type="number" name="montant" step="0.001" min="0.001"
                           max="{{ $facture->reste_a_payer }}"
                           class="form-control" value="{{ old('montant') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Date du paiement / remise</label>
                    <input type="date" name="date_paiement" class="form-control"
                           value="{{ old('date_paiement', date('Y-m-d')) }}" required>
                </div>

                <div id="champs-cheque" class="border rounded p-3 mb-3">
                    <h5>Informations du chèque</h5>

                    <div class="mb-3">
                        <label class="form-label">Numéro / référence du chèque</label>
                        <input name="reference" class="form-control"
                               value="{{ old('reference') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Banque</label>
                        <input name="banque" class="form-control"
                               value="{{ old('banque') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Titulaire du chèque</label>
                        <input name="titulaire" class="form-control"
                               value="{{ old('titulaire') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date inscrite sur le chèque</label>
                        <input type="date" name="date_cheque" class="form-control"
                               value="{{ old('date_cheque') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date prévue du dépôt à la banque</label>
                        <input type="date" name="date_depot_prevue" class="form-control"
                               value="{{ old('date_depot_prevue') }}">
                    </div>
                </div>

                <div id="champs-cambiale" class="border rounded p-3 mb-3">
                    <h5>Informations de la cambiale</h5>

                    <div class="mb-3">
                        <label class="form-label">Référence de la cambiale</label>
                        <input name="reference_cambiale" id="reference_cambiale"
                               class="form-control" value="{{ old('reference_cambiale') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date d'échéance</label>
                        <input type="date" name="date_echeance" id="date_echeance"
                               class="form-control" value="{{ old('date_echeance') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observation</label>
                    <textarea name="observation" class="form-control">{{ old('observation') }}</textarea>
                </div>

                <button class="btn btn-success" type="submit">
                    Enregistrer le paiement
                </button>

                <a href="{{ route('factures.index') }}" class="btn btn-secondary">
                    Retour aux factures
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const type = document.getElementById('type');
    const cheque = document.getElementById('champs-cheque');
    const cambiale = document.getElementById('champs-cambiale');

    function actualiserChamps() {
        cheque.hidden = type.value !== 'cheque';
        cambiale.hidden = type.value !== 'cambiale';

        document.querySelector('[name="date_cheque"]').required =
            type.value === 'cheque';

        document.querySelector('[name="date_depot_prevue"]').required =
            type.value === 'cheque';

        document.getElementById('date_echeance').required =
            type.value === 'cambiale';
    }

    type.addEventListener('change', actualiserChamps);
    actualiserChamps();
});
</script>
@endsection
