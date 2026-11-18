<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pointage extends Model
{
   
    protected $fillable = [
        'employe_id',
        'date_pointage',
        'heure_arrivee',
        'heure_depart',
        'retard_minutes',
        'travail_minutes',
        'supplementaires_minutes',
        'statut',
        'observation',
    ];

    protected $casts = [
        'date_pointage' => 'date',
    ];

    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class);
    }
}
