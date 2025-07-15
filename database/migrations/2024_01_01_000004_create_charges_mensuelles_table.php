<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('charges_mensuelles', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->foreignId('appartement_id')->nullable()->constrained('appartements')->onDelete('cascade');
            $table->date('date_paiement');
            $table->decimal('montant', 10, 2);
            $table->boolean('recurrent')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('charges_mensuelles');
    }
};