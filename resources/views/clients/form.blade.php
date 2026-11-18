
<div class="mb-3">
    <label for="nom" class="form-label">Nom *</label>
    <input type="text" name="nom" id="nom"
           class="form-control" required
           value="{{ old('nom', $client->nom ?? '') }}">
    @error('nom')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="prenom" class="form-label">Prénom</label>
    <input type="text" name="prenom" id="prenom"
           class="form-control"
           value="{{ old('prenom', $client->prenom ?? '') }}">
</div>

<div class="mb-3">
    <label for="telephone" class="form-label">Téléphone</label>
    <input type="text" name="telephone" id="telephone"
           class="form-control"
           value="{{ old('telephone', $client->telephone ?? '') }}">
</div>

<div class="mb-3">
    <label for="adresse" class="form-label">Adresse</label>
    <textarea name="adresse" id="adresse"
              class="form-control" rows="3">{{ old('adresse', $client->adresse ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="matricule_fiscal" class="form-label">Matricule fiscal</label>
    <input type="text" name="matricule_fiscal" id="matricule_fiscal"
           class="form-control"
           value="{{ old('matricule_fiscal', $client->matricule_fiscal ?? '') }}">
</div>

<button type="submit" class="btn btn-success">Enregistrer</button>
<a href="{{ route('clients.index') }}" class="btn btn-secondary">Annuler</a>
