
@extends('layouts.app')

@section('content')

<div class="container" style="max-width: 800px; margin: 30px auto;">

    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">

        <h2 style="color: #172554; margin-bottom: 10px;">
            Correction du pointage
        </h2>

        <p style="color: #64748b; margin-bottom: 25px;">
            Employé :
            <strong>
                {{ $pointage->employe->user->name ?? 'Employé' }}
            </strong>
        </p>

        @if ($errors->any())
            <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pointages.update', $pointage->id) }}" method="POST">

            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">
                    Date du pointage
                </label>

                <input
                    type="date"
                    name="date_pointage"
                    value="{{ old('date_pointage', $pointage->date_pointage->format('Y-m-d')) }}"
                    required
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;"
                >
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">
                    Heure d'arrivée
                </label>

                <input
                    type="time"
                    name="heure_arrivee"
                    value="{{ old('heure_arrivee', $pointage->heure_arrivee ? substr($pointage->heure_arrivee, 0, 5) : '') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;"
                >
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">
                    Heure de départ
                </label>

                <input
                    type="time"
                    name="heure_depart"
                    value="{{ old('heure_depart', $pointage->heure_depart ? substr($pointage->heure_depart, 0, 5) : '') }}"
                    style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px;"
                >
            </div>

            <div style="background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                <strong>Horaire normal de l'employé :</strong>
                <p style="margin: 8px 0 0;">
                    Début : {{ substr($pointage->employe->heure_debut, 0, 5) }}
                    <br>
                    Fin : {{ substr($pointage->employe->heure_fin, 0, 5) }}
                </p>
                <small style="color: #64748b;">
                    Le retard et les heures travaillées seront recalculés automatiquement après l'enregistrement.
                </small>
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">

                <button
                    type="submit"
                    style="background: #2563eb; color: white; border: none; padding: 12px 22px; border-radius: 8px; cursor: pointer; font-weight: bold;"
                >
                    Enregistrer la correction
                </button>

                <a
                    href="{{ route('pointages.index') }}"
                    style="background: #e2e8f0; color: #334155; padding: 12px 22px; border-radius: 8px; text-decoration: none;"
                >
                    Annuler
                </a>

            </div>

        </form>

    </div>

</div>

@endsection
