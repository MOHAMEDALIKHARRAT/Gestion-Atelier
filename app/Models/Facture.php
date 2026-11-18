<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facture extends Model
{
    protected $fillable = [
        'numero',
        'client_id',
        'date_facture',
        'total_ht',
        'tva',
        'timbre',
        'remise',
        'total_ttc',
        'montant_paye',
        'reste_a_payer',
        'statut_paiement',
    ];

    protected function casts(): array
    {
        return [
            'date_facture' => 'date',
            'total_ht' => 'decimal:3',
            'tva' => 'decimal:3',
            'timbre' => 'decimal:3',
            'remise' => 'decimal:3',
            'total_ttc' => 'decimal:3',
            'montant_paye' => 'decimal:3',
            'reste_a_payer' => 'decimal:3',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneFacture::class);
    }
    public function paiements(): HasMany
{
    return $this->hasMany(Paiement::class);
}
}
