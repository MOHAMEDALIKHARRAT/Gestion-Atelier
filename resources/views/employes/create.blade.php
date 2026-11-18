
@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2 class="mb-4">Ajouter un nouvel employé</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Veuillez corriger les erreurs :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('employes.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <h5 class="mb-3">1. Informations personnelles</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nom *</label>
                        <input type="text" name="nom"
                               class="form-control"
                               value="{{ old('nom') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Prénom *</label>
                        <input type="text" name="prenom"
                               class="form-control"
                               value="{{ old('prenom') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Numéro de CIN *</label>
                        <input type="text" name="cin"
                               class="form-control"
                               value="{{ old('cin') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Téléphone</label>
                        <input type="text" name="telephone"
                               class="form-control"
                               value="{{ old('telephone') }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label>Adresse</label>
                        <textarea name="adresse" class="form-control"
                                  rows="2">{{ old('adresse') }}</textarea>
                    </div>

                    <div class="col-12 mb-3">
                        <label>Photo ou scan de la carte d'identité</label>
                        <input type="file" name="photo_cin"
                               class="form-control"
                               accept=".jpg,.jpeg,.png">
                        <small class="text-muted">
                            Formats acceptés : JPG, JPEG et PNG. Maximum 2 Mo.
                        </small>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">2. Compte de connexion</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Email *</label>
                        <input type="email" name="email"
                               class="form-control"
                               value="{{ old('email') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Mot de passe *</label>
                        <input type="password" name="password"
                               class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Confirmer le mot de passe *</label>
                        <input type="password" name="password_confirmation"
                               class="form-control" required>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">3. Informations professionnelles</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Poste</label>
                        <input type="text" name="poste"
                               class="form-control"
                               value="{{ old('poste') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Date d'embauche</label>
                        <input type="date" name="date_embauche"
                               class="form-control"
                               value="{{ old('date_embauche') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Salaire mensuel (TND) *</label>
                        <input type="number" step="0.001" min="0"
                               name="salaire_mensuel"
                               class="form-control"
                               value="{{ old('salaire_mensuel') }}" required>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">4. Horaires de travail</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Heure d'arrivée *</label>
                        <input type="time" name="heure_debut"
                               class="form-control"
                               value="{{ old('heure_debut', '08:00') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Heure de départ *</label>
                        <input type="time" name="heure_fin"
                               class="form-control"
                               value="{{ old('heure_fin', '17:00') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Pause en minutes *</label>
                        <input type="number" name="pause_minutes"
                               min="0" class="form-control"
                               value="{{ old('pause_minutes', 60) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Tolérance de retard (minutes) *</label>
                        <input type="number" name="tolerance_retard"
                               min="0" class="form-control"
                               value="{{ old('tolerance_retard', 5) }}" required>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">
                        Enregistrer l'employé
                    </button>

                    <a href="{{ route('employes.index') }}"
                       class="btn btn-secondary">
                        Retour
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
