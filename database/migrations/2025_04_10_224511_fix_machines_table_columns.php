<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supprimer la colonne disponible qui contient une date
        Schema::table('machines', function (Blueprint $table) {
            $table->dropColumn('disponible');
        });

        // Renommer est_disponible en disponible
        Schema::table('machines', function (Blueprint $table) {
            $table->renameColumn('est_disponible', 'disponible');
        });

        // Mettre à jour les valeurs de statut en fonction de disponible
        DB::table('machines')->where('disponible', 1)->update(['statut' => 'disponible']);
        DB::table('machines')->where('disponible', 0)->update(['statut' => 'non_disponible']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir en arrière si nécessaire
        Schema::table('machines', function (Blueprint $table) {
            $table->renameColumn('disponible', 'est_disponible');
            $table->timestamp('disponible')->nullable();
        });
    }
};
