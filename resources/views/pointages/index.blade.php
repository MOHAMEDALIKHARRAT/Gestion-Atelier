
@extends('layouts.app')

@section('content')

<div class="container" style="padding: 25px;">

    {{-- En-tête --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="color: #172554; margin: 0;">
                Gestion des pointages
            </h1>
            <p style="color: #64748b; margin-top: 8px;">
                Suivi quotidien des heures de travail
            </p>
        </div>

        <span style="background: #2563eb; color: white; padding: 10px 15px; border-radius: 8px; font-weight: bold;">
            {{ $date->format('d/m/Y') }}
        </span>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Statistiques --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 45px;">

        <div style="background: white; padding: 22px; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">
            <p style="color: #64748b; margin: 0;">Total employés</p>
            <h2 style="font-size: 32px; margin: 10px 0; color: #172554;">
                {{ $totalEmployes }}
            </h2>
        </div>

        <div style="background: white; padding: 22px; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">
            <p style="color: #64748b; margin: 0;">Présents aujourd'hui</p>
            <h2 style="font-size: 32px; margin: 10px 0; color: #16a34a;">
                {{ $totalPresents }}
            </h2>
        </div>

        <div style="background: white; padding: 22px; border-radius: 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">
            <p style="color: #64748b; margin: 0;">Retards enregistrés</p>
            <h2 style="font-size: 32px; margin: 10px 0; color: #e11d48;">
                {{ $totalRetards }}
            </h2>
        </div>

    </div>

    {{-- Tableau des employés --}}
    <div style="background: white; padding: 20px; border-radius: 18px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">

        <h2 style="font-size: 23px; color: #172554; margin-bottom: 20px;">
            Pointage des employés
        </h2>

        <div style="overflow-x: auto;">

            <table style="width: 100%; border-collapse: collapse; min-width: 1000px;">

                <thead>
                    <tr style="background: #f1f5f9; text-align: left;">
                        <th style="padding: 15px;">Employé</th>
                        <th style="padding: 15px;">Horaire prévu</th>
                        <th style="padding: 15px;">Arrivée</th>
                        <th style="padding: 15px;">Départ</th>
                        <th style="padding: 15px;">Retard</th>
                        <th style="padding: 15px;">Travail</th>
                        <th style="padding: 15px;">Supplémentaires</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($employes as $employe)

                        @php
                            $pointage = $pointages->firstWhere('employe_id', $employe->id);

                            $travail = $pointage ? (int) $pointage->travail_minutes : 0;
                            $supplementaires = $pointage ? (int) $pointage->supplementaires_minutes : 0;

                            $heuresTravail = intdiv($travail, 60);
                            $minutesTravail = $travail % 60;

                            $heuresSup = intdiv($supplementaires, 60);
                            $minutesSup = $supplementaires % 60;
                        @endphp

                        <tr style="border-bottom: 1px solid #e2e8f0;">

                            {{-- Employé --}}
                            <td style="padding: 15px;">
                                <strong style="color: #0f172a;">
                                    {{ $employe->user->name ?? 'Employé' }}
                                </strong>
                                <br>
                                <span style="color: #64748b; font-size: 14px;">
                                    {{ $employe->poste ?? '' }}
                                </span>
                            </td>

                            {{-- Horaire prévu --}}
                            <td style="padding: 15px;">
                                {{ substr($employe->heure_debut, 0, 5) }}
                                -
                                {{ substr($employe->heure_fin, 0, 5) }}
                            </td>

                            {{-- Arrivée --}}
                            <td style="padding: 15px; color: #059669; font-weight: bold;">
                                @if($pointage && $pointage->heure_arrivee)
                                    {{ substr($pointage->heure_arrivee, 0, 5) }}
                                @else
                                    <span style="color: #94a3b8;">--:--</span>
                                @endif
                            </td>

                            {{-- Départ --}}
                            <td style="padding: 15px; color: #2563eb; font-weight: bold;">
                                @if($pointage && $pointage->heure_depart)
                                    {{ substr($pointage->heure_depart, 0, 5) }}
                                @else
                                    <span style="color: #94a3b8;">--:--</span>
                                @endif
                            </td>

                            {{-- Retard --}}
                            <td style="padding: 15px;">
                                @if($pointage && $pointage->heure_arrivee)
                                    @if($pointage->retard_minutes > 0)
                                        <span style="background: #e11d48; color: white; padding: 5px 10px; border-radius: 7px; font-weight: bold;">
                                            {{ $pointage->retard_minutes }} min
                                        </span>
                                    @else
                                        <span style="background: #dcfce7; color: #166534; padding: 5px 10px; border-radius: 7px;">
                                            À l'heure
                                        </span>
                                    @endif
                                @else
                                    <span style="color: #94a3b8;">Non enregistré</span>
                                @endif
                            </td>

                            {{-- Travail --}}
                            <td style="padding: 15px;">
                                {{ $heuresTravail }}h {{ $minutesTravail }}min
                            </td>

                            {{-- Heures supplémentaires --}}
                            <td style="padding: 15px;">
                                {{ $heuresSup }}h {{ $minutesSup }}min
                            </td>

                            {{-- Actions --}}
                            <td style="padding: 15px;">

                                @if(!$pointage || !$pointage->heure_arrivee)

                                    <form action="{{ route('pointages.arriver') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="employe_id" value="{{ $employe->id }}">

                                        <button type="submit"
                                            style="background: #16a34a; color: white; border: none; padding: 8px 12px; border-radius: 7px; cursor: pointer; margin-bottom: 5px;">
                                            Arrivée
                                        </button>
                                    </form>

                                @elseif(!$pointage->heure_depart)

                                    <form action="{{ route('pointages.partir') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="employe_id" value="{{ $employe->id }}">

                                        <button type="submit"
                                            style="background: #2563eb; color: white; border: none; padding: 8px 12px; border-radius: 7px; cursor: pointer; margin-bottom: 5px;">
                                            Départ
                                        </button>
                                    </form>

                                @else

                                    <span style="background: #16a34a; color: white; padding: 7px 10px; border-radius: 7px; display: inline-block; margin-bottom: 5px;">
                                        Journée terminée
                                    </span>

                                @endif

                                @if($pointage)

                                    <br>

                                    <a href="{{ route('pointages.edit', $pointage->id) }}"
                                       style="background: #2563eb; color: white; padding: 8px 12px; border-radius: 7px; text-decoration: none; display: inline-block; margin-top: 5px;">
                                        Modifier
                                    </a>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" style="padding: 25px; text-align: center; color: #64748b;">
                                Aucun employé enregistré.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection
