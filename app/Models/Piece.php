<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Piece extends Model
{
     protected $fillable = [
       'nom',
        'reference',
        'quantite',
        'prix_achat',
        'prix_vente',
        'seuil_alerte',
        'fournisseur_id',
    ];
     public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }
}
