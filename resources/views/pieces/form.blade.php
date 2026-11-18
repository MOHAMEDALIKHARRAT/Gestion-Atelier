<div class="mb-3">
    <label for="nom" class="form-label">Nom de la pièce</label>
    <input
        type="text"
        name="nom"
        id="nom"
        class="form-control"
        value="{{ old('nom', $piece->nom ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label for="reference" class="form-label">Référence</label>
    <input
        type="text"
        name="reference"
        id="reference"
        class="form-control"
        value="{{ old('reference', $piece->reference ?? '') }}"
        required
    >
</div>

<div class="mb-3">
    <label for="quantite" class="form-label">Quantité en stock</label>
    <input
        type="number"
        name="quantite"
        id="quantite"
        class="form-control"
        min="0"
        value="{{ old('quantite', $piece->quantite ?? 0) }}"
        required
    >
</div>

<div class="mb-3">
    <label for="prix_achat" class="form-label">Prix d'achat (TND)</label>
    <input
        type="number"
        name="prix_achat"
        id="prix_achat"
        class="form-control"
        min="0"
        step="0.001"
        value="{{ old('prix_achat', $piece->prix_achat ?? 0) }}"
        required
    >
</div>

<div class="mb-3">
    <label for="prix_vente" class="form-label">Prix de vente (TND)</label>
    <input
        type="number"
        name="prix_vente"
        id="prix_vente"
        class="form-control"
        min="0"
        step="0.001"
        value="{{ old('prix_vente', $piece->prix_vente ?? 0) }}"
        required
    >
</div>

<div class="mb-3">
    <label for="seuil_alerte" class="form-label">Seuil d'alerte du stock</label>
    <input
        type="number"
        name="seuil_alerte"
        id="seuil_alerte"
        class="form-control"
        min="0"
        value="{{ old('seuil_alerte', $piece->seuil_alerte ?? 5) }}"
        required
    >
</div>

<div class="mb-3">
    <label for="fournisseur_id" class="form-label">
        Fournisseur
    </label>

    <select
        name="fournisseur_id"
        id="fournisseur_id"
        class="form-select"
    >
        <option value="">-- Choisir un fournisseur --</option>

        @foreach ($fournisseurs as $fournisseur)
            <option
                value="{{ $fournisseur->id }}"
                @selected(
                    (string) old(
                        'fournisseur_id',
                        isset($piece) ? $piece->fournisseur_id : ''
                    ) === (string) $fournisseur->id
                )
            >
                {{ $fournisseur->nom }}
            </option>
        @endforeach
    </select>
</div>


<button type="submit" class="btn btn-primary">Enregistrer</button>
<a href="{{ route('pieces.index') }}" class="btn btn-secondary">Annuler</a>