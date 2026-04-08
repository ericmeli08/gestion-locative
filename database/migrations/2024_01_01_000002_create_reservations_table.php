<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_entree');
            $table->dateTime('date_sortie');
            $table->string('client');
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->enum('plateforme', ['airbnb', 'booking', 'direct']);
            $table->foreignId('appartement_id')->constrained('appartements')->onDelete('cascade');
            $table->decimal('prix_nuit', 8, 2);
            $table->integer('nombre_nuits');
            $table->decimal('revenus_totaux', 10, 2);
            $table->dateTime('date_paiement')->nullable();
            $table->enum('statut_paiement', ['paid', 'unpaid', 'partial'])->default('unpaid');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
