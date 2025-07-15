<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('disponibilites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appartement_id')->constrained('appartements')->onDelete('cascade');
            $table->date('date');
            $table->enum('statut', ['available', 'occupied', 'maintenance'])->default('available');
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['appartement_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('disponibilites');
    }
};