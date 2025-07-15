<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taux_occupations', function (Blueprint $table) {
            $table->id();
            $table->date('mois');
            $table->foreignId('appartement_id')->constrained('appartements')->onDelete('cascade');
            $table->integer('jours_disponibles');
            $table->integer('jours_loues');
            $table->decimal('taux_occupation', 5, 2);
            $table->timestamps();
            
            $table->unique(['mois', 'appartement_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('taux_occupations');
    }
};