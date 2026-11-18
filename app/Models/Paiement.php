<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paiement extends Model
{
     protected $fillable = [
        'facture_id',
        'type',
        'montant',
        'date_paiement',
        'reference',
        'banque',
        'titulaire',
        'date_cheque',
        'date_depot_prevue',
        'date_depot_effectif',
        'date_encaissement',
        'date_echeance',
        'statut',
        'observation',
        'rappel_veille_envoye',
        'rappel_jour_envoye',
        'rappel_retard_envoye',
    ];

    protected function casts(): array
    {
        return [
            'montant' => 'decimal:3',
            'date_paiement' => 'date',
            'date_cheque' => 'date',
            'date_depot_prevue' => 'date',
            'date_depot_effectif' => 'date',
            'date_encaissement' => 'date',
            'date_echeance' => 'date',
            'rappel_veille_envoye' => 'boolean',
            'rappel_jour_envoye' => 'boolean',
            'rappel_retard_envoye' => 'boolean',
        ];
    }

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }
}
