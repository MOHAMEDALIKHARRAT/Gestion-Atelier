<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();

            $table->date('date_facture');
            $table->decimal('total_ht', 10, 3)->default(0);
            $table->decimal('tva', 10, 3)->default(0);
            $table->decimal('timbre', 10, 3)->default(1);
            $table->decimal('remise', 10, 3)->default(0);
            $table->decimal('total_ttc', 10, 3)->default(0);
            $table->decimal('montant_paye', 10, 3)->default(0);
            $table->decimal('reste_a_payer', 10, 3)->default(0);

            $table->string('statut_paiement')->default('impayee');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
