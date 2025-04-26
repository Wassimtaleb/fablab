<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Corriger les valeurs hors limites
        DB::statement('UPDATE reservations SET duree = 6 WHERE duree > 6');
        DB::statement('UPDATE reservations SET duree = 1 WHERE duree < 1 OR duree IS NULL');
        // Ajouter une contrainte CHECK pour MySQL 8+ ou MariaDB 10.2+
        DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_duree CHECK (duree BETWEEN 1 AND 6)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer la contrainte CHECK (si supportée)
        DB::statement('ALTER TABLE reservations DROP CONSTRAINT chk_duree');
    }
};
