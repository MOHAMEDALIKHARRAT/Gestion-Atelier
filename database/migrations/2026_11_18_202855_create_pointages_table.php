<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pointages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employe_id')
                ->constrained('employes')
                ->cascadeOnDelete();

            $table->date('date_pointage');

            $table->time('heure_arrivee')->nullable();
            $table->time('heure_depart')->nullable();

            $table->unsignedInteger('retard_minutes')->default(0);

            $table->unsignedInteger('travail_minutes')->default(0);

            $table->unsignedInteger('supplementaires_minutes')->default(0);

            $table->enum('statut', [
                'present',
                'absent',
                'conge',
                'permission'
            ])->default('present');

            $table->text('observation')->nullable();

            $table->timestamps();

            $table->unique(['employe_id', 'date_pointage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pointages');
    }
};