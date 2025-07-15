<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type_depense');
            $table->text('description');
            $table->foreignId('appartement_id')->nullable()->constrained('appartements')->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->string('facture_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('depenses');
    }
};