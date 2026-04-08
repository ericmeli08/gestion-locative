<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('degat_reparations', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('appartement_id')->constrained('appartements')->onDelete('cascade');
            $table->string('type_degat');
            $table->text('description');
            $table->text('solution')->nullable();
            $table->decimal('cout', 10, 2)->nullable();
            $table->enum('statut', ['signale', 'en_cours', 'termine'])->default('signale');
            $table->date('date_reparation')->nullable();
            $table->string('reparateur')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('degat_reparations');
    }
};