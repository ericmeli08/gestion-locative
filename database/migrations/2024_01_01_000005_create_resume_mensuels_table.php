<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resume_mensuels', function (Blueprint $table) {
            $table->id();
            $table->date('mois');
            $table->decimal('revenus_totaux', 12, 2)->default(0);
            $table->decimal('depenses_totales', 12, 2)->default(0);
            $table->decimal('charges_totales', 12, 2)->default(0);
            $table->decimal('benefice_net', 12, 2)->default(0);
            $table->timestamps();
            
            $table->unique('mois');
        });
    }

    public function down()
    {
        Schema::dropIfExists('resume_mensuels');
    }
};