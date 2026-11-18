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
        Schema::table('employes', function (Blueprint $table) {
            $table->string('cin', 20)->nullable()->unique();
            $table->text('adresse')->nullable();
            $table->string('photo_cin')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('employes', function (Blueprint $table) {
            $table->dropUnique(['cin']);
            $table->dropColumn(['cin', 'adresse', 'photo_cin']);
        });
    }
    
};
