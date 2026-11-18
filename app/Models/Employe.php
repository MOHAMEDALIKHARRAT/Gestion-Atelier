<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employe extends Model
{  protected $fillable = [
        'user_id',
        'telephone',
        'poste',
        'date_embauche',
        'salaire_mensuel',
        'heure_debut',
        'heure_fin',
        'pause_minutes',
        'tolerance_retard',
        'cin',
        'adresse',
        'photo_cin',
    ];

    protected $casts = [
        'date_embauche' => 'date',
        'salaire_mensuel' => 'decimal:3',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function pointages(): HasMany
{
    return $this->hasMany(Pointage::class);
}
}
