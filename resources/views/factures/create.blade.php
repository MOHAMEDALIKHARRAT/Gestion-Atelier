@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Créer une facture</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('factures.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Client *</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">Choisir un client</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}"
                                    @selected(old('client_id') == $client->id)>
                                    {{ $client->nom }} {{ $client->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date de facture *</label>
                        <input type="date" name="date_facture"
                               class="form-control" required
                               value="{{ old('date_facture', date('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Lignes de facture</strong>
                <button type="button" id="ajouter-ligne" class="btn btn-primary btn-sm">
                    + Ajouter une ligne
                </button>
            </div>

            <div class="card-body">
                @php
                    // Types de lignes disponibles (modifiable ici uniquement)
                    $types = [
                        'piece'       => 'Pièce',
                        'main_oeuvre' => "Main d'œuvre",
                        'deplacement' => 'Déplacement',
                        'autre'       => 'Autre',
                    ];

                    $anciennesLignes = old('lignes', [[
                        'type' => 'piece',
                        'piece_id' => '',
                        'designation' => '',
                        'quantite' => 1,
                        'prix_unitaire_ht' => ''
                    ]]);

                    // Plus grand index + 1 (évite d'écraser une ligne après une erreur de validation)
                    $prochainIndex = empty($anciennesLignes) ? 0 : max(array_keys($anciennesLignes)) + 1;

                    // Données des pièces pour le JavaScript
                    $piecesJs = $pieces->map(fn ($p) => [
                        'id'    => $p->id,
                        'nom'   => $p->nom,
                        'prix'  => (float) $p->prix_vente,
                        'stock' => $p->quantite,
                    ])->values();
                @endphp

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Pièce</th>
                                <th>Désignation</th>
                                <th>Quantité / Heures</th>
                                <th>Prix unitaire HT</th>
                                <th>Total HT</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="lignes" data-prochain-index="{{ $prochainIndex }}">
                            @foreach ($anciennesLignes as $i => $ligne)
                                <tr class="ligne">
                                    <td>
                                        <select name="lignes[{{ $i }}][type]"
                                                class="form-select type-ligne">
                                            @foreach ($types as $valeur => $libelle)
                                                <option value="{{ $valeur }}"
                                                    @selected(($ligne['type'] ?? 'piece') === $valeur)>
                                                    {{ $libelle }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select name="lignes[{{ $i }}][piece_id]"
                                                class="form-select piece-select">
                                            <option value="">Choisir</option>
                                            @foreach ($pieces as $piece)
                                                <option value="{{ $piece->id }}"
                                                    data-nom="{{ $piece->nom }}"
                                                    data-prix="{{ $piece->prix_vente }}"
                                                    data-stock="{{ $piece->quantite }}"
                                                    @selected(($ligne['piece_id'] ?? '') == $piece->id)>
                                                    {{ $piece->nom }} (stock : {{ $piece->quantite }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input name="lignes[{{ $i }}][designation]"
                                               class="form-control designation"
                                               value="{{ $ligne['designation'] ?? '' }}"
                                               required>
                                    </td>

                                    <td>
                                        <input type="number" step="0.01" min="0.01"
                                               name="lignes[{{ $i }}][quantite]"
                                               class="form-control quantite"
                                               value="{{ $ligne['quantite'] ?? 1 }}"
                                               required>
                                    </td>

                                    <td>
                                        <input type="number" step="0.001" min="0"
                                               name="lignes[{{ $i }}][prix_unitaire_ht]"
                                               class="form-control prix"
                                               value="{{ $ligne['prix_unitaire_ht'] ?? '' }}"
                                               required>
                                    </td>

                                    <td class="total-ligne">0.000 TND</td>

                                    <td>
                                        <button type="button"
                                                class="btn btn-danger btn-sm supprimer-ligne">
                                            X
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">Remise (TND)</label>
                            <input type="number" name="remise" id="remise"
                                   class="form-control" min="0" step="0.001"
                                   value="{{ old('remise', 0) }}">
                        </div>

                        <p>Sous-total HT : <strong id="total-ht">0.000 TND</strong></p>
                        <p>TVA (19 %) : <strong id="total-tva">0.000 TND</strong></p>
                        <p>Timbre fiscal : <strong>1.000 TND</strong></p>
                        <h4>Total TTC : <span id="total-ttc">1.000 TND</span></h4>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer la facture</button>
        <a href="{{ route('factures.index') }}" class="btn btn-secondary">Annuler</a>
    </form>
</div>

{{-- Données PHP -> JS (aucune syntaxe Blade dans le script principal) --}}
<script type="application/json" id="pieces-data">@json($piecesJs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>
<script type="application/json" id="types-data">@json($types, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('lignes');
    const pieces = JSON.parse(document.getElementById('pieces-data').textContent);
    const types = JSON.parse(document.getElementById('types-data').textContent);
    let prochainIndex = parseInt(tbody.dataset.prochainIndex, 10) || 0;

    function echapper(texte) {
        return String(texte)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function calculer() {
        let ht = 0;

        tbody.querySelectorAll('.ligne').forEach(function (ligne) {
            const qte = parseFloat(ligne.querySelector('.quantite').value) || 0;
            const prix = parseFloat(ligne.querySelector('.prix').value) || 0;
            const total = qte * prix;

            ligne.querySelector('.total-ligne').textContent =
                total.toFixed(3) + ' TND';

            ht += total;
        });

        const remise = Math.min(
            Math.max(parseFloat(document.getElementById('remise').value) || 0, 0),
            ht
        );
        const base = ht - remise;
        const tva = base * 0.19;
        const ttc = base + tva + 1;

        document.getElementById('total-ht').textContent = ht.toFixed(3) + ' TND';
        document.getElementById('total-tva').textContent = tva.toFixed(3) + ' TND';
        document.getElementById('total-ttc').textContent = ttc.toFixed(3) + ' TND';
    }

    function mettreAJourType(ligne) {
        const type = ligne.querySelector('.type-ligne').value;
        const select = ligne.querySelector('.piece-select');
        const designation = ligne.querySelector('.designation');

        select.disabled = type !== 'piece';
        select.required = type === 'piece';

        if (type !== 'piece') {
            select.value = '';
        }

        if (type === 'main_oeuvre' && !designation.value) {
            designation.value = "Main d'œuvre";
        }

        designation.required = true;
    }

    function creerOptionsType() {
        return Object.keys(types).map(function (valeur) {
            return '<option value="' + echapper(valeur) + '">' +
                echapper(types[valeur]) + '</option>';
        }).join('');
    }

    function creerOptionsPiece() {
        return '<option value="">Choisir</option>' + pieces.map(function (p) {
            return '<option value="' + p.id + '" data-nom="' + echapper(p.nom) +
                '" data-prix="' + p.prix + '" data-stock="' + p.stock + '">' +
                echapper(p.nom) + ' (stock : ' + p.stock + ')</option>';
        }).join('');
    }

    function ajouterLigne() {
        const i = prochainIndex++;
        const tr = document.createElement('tr');
        tr.className = 'ligne';

        tr.innerHTML = `
            <td>
                <select name="lignes[${i}][type]" class="form-select type-ligne">
                    ${creerOptionsType()}
                </select>
            </td>
            <td>
                <select name="lignes[${i}][piece_id]" class="form-select piece-select">
                    ${creerOptionsPiece()}
                </select>
            </td>
            <td>
                <input name="lignes[${i}][designation]" class="form-control designation" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" value="1"
                    name="lignes[${i}][quantite]" class="form-control quantite" required>
            </td>
            <td>
                <input type="number" step="0.001" min="0"
                    name="lignes[${i}][prix_unitaire_ht]" class="form-control prix" required>
            </td>
            <td class="total-ligne">0.000 TND</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm supprimer-ligne">X</button>
            </td>
        `;

        tbody.appendChild(tr);
        mettreAJourType(tr);
        calculer();
    }

    tbody.addEventListener('change', function (event) {
        const ligne = event.target.closest('.ligne');
        if (!ligne) return;

        if (event.target.classList.contains('type-ligne')) {
            mettreAJourType(ligne);
        }

        if (event.target.classList.contains('piece-select')) {
            const option = event.target.selectedOptions[0];
            if (option && option.value) {
                ligne.querySelector('.designation').value = option.dataset.nom;
                ligne.querySelector('.prix').value = option.dataset.prix;
            }
        }

        calculer();
    });

    tbody.addEventListener('input', calculer);

    tbody.addEventListener('click', function (event) {
        if (event.target.classList.contains('supprimer-ligne')) {
            if (tbody.querySelectorAll('.ligne').length > 1) {
                event.target.closest('.ligne').remove();
                calculer();
            } else {
                alert('Une facture doit contenir au moins une ligne.');
            }
        }
    });

    document.getElementById('ajouter-ligne').addEventListener('click', ajouterLigne);
    document.getElementById('remise').addEventListener('input', calculer);

    tbody.querySelectorAll('.ligne').forEach(function (ligne) {
        mettreAJourType(ligne);
    });

    calculer();
});
</script>
@endsection