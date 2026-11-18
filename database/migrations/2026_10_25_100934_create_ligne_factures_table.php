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
    {Schema::create('ligne_factures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('facture_id')
                ->constrained('factures')
                ->cascadeOnDelete();

            $table->foreignId('piece_id')
                ->nullable()
                ->constrained('pieces')
                ->nullOnDelete();

            $table->string('designation');
            $table->string('type')->default('piece');
            $table->decimal('quantite', 10, 2)->default(1);
            $table->decimal('prix_unitaire_ht', 10, 3);
            $table->decimal('total_ligne_ht', 10, 3);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_factures');
    }
};
