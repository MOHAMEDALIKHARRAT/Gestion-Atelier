<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    Schema::create('paiements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('facture_id')
                ->constrained('factures')
                ->restrictOnDelete();

            $table->enum('type', ['especes', 'cheque', 'cambiale']);
            $table->decimal('montant', 10, 3);
            $table->date('date_paiement');

            $table->string('reference')->nullable();
            $table->string('banque')->nullable();
            $table->string('titulaire')->nullable();

            $table->date('date_cheque')->nullable();
            $table->date('date_depot_prevue')->nullable();
            $table->date('date_depot_effectif')->nullable();
            $table->date('date_encaissement')->nullable();
            $table->date('date_echeance')->nullable();

            $table->enum('statut', [
                'paye',
                'a_traiter',
                'depose',
                'encaisse',
                'rejete',
            ])->default('paye');

            $table->text('observation')->nullable();

            $table->boolean('rappel_veille_envoye')->default(false);
            $table->boolean('rappel_jour_envoye')->default(false);
            $table->boolean('rappel_retard_envoye')->default(false);

            $table->timestamps();

            $table->index(['type', 'statut']);
            $table->index('date_depot_prevue');
            $table->index('date_echeance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
