<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('element');
            $table->integer('quantite_mois');
            $table->integer('utilise')->default(0);
            $table->integer('reste');
            $table->integer('seuil');
            $table->boolean('a_racheter')->default(false);
            $table->foreignId('appartement_id')->nullable()->constrained('appartements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};