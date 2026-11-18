
<div class="mb-3">
    <label for="nom" class="form-label">Nom du fournisseur *</label>
    <input
        type="text"
        id="nom"
        name="nom"
        class="form-control"
        value="{{ old('nom', isset($fournisseur) ? $fournisseur->nom : '') }}"
        required
    >
</div>

<div class="mb-3">
    <label for="telephone" class="form-label">Téléphone</label>
    <input
        type="text"
        id="telephone"
        name="telephone"
        class="form-control"
        value="{{ old('telephone', isset($fournisseur) ? $fournisseur->telephone : '') }}"
    >
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input
        type="email"
        id="email"
        name="email"
        class="form-control"
        value="{{ old('email', isset($fournisseur) ? $fournisseur->email : '') }}"
    >
</div>

<div class="mb-3">
    <label for="adresse" class="form-label">Adresse</label>
    <textarea
        id="adresse"
        name="adresse"
        class="form-control"
        rows="3"
    >{{ old('adresse', isset($fournisseur) ? $fournisseur->adresse : '') }}</textarea>
</div>

<div class="mb-3">
    <label for="matricule_fiscal" class="form-label">Matricule fiscal</label>
    <input
        type="text"
        id="matricule_fiscal"
        name="matricule_fiscal"
        class="form-control"
        value="{{ old('matricule_fiscal', isset($fournisseur) ? $fournisseur->matricule_fiscal : '') }}"
    >
</div>

<button type="submit" class="btn btn-primary">
    Enregistrer
</button>

<a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary">
    Annuler
</a>
