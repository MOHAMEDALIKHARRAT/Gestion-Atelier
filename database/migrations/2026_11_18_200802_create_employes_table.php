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
        Schema::create('employes', function (Blueprint $table) {
            $table->id();

            // Lien avec le compte utilisateur
            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            // Informations professionnelles
            $table->string('telephone')->nullable();
            $table->string('poste')->nullable();
            $table->date('date_embauche')->nullable();

            // Salaire personnel de l'employe
            $table->decimal('salaire_mensuel', 10, 3)->default(0);

            // Horaires de travail
            $table->time('heure_debut')->default('08:00:00');
            $table->time('heure_fin')->default('17:00:00');
            $table->decimal('pause_minutes', 6, 2)->default(60);

            // Tolérance de retard
            $table->unsignedInteger('tolerance_retard')->default(5);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employes');
    }
};
