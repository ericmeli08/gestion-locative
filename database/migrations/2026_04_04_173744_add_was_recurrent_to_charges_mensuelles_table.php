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
        Schema::table('charge_mensuelles', function (Blueprint $table) {
            // Mémorise si la charge était récurrente avant désactivation de l'appart
            $table->boolean('was_recurrent')->default(false)->after('recurrent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charge_mensuelles', function (Blueprint $table) {
            $table->dropColumn('was_recurrent');
        });
    }
};
