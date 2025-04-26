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
        // Utiliser une requête brute pour supprimer le default sur la colonne date_reservation
        DB::statement('ALTER TABLE reservations MODIFY date_reservation DATETIME NOT NULL;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre le default CURRENT_TIMESTAMP si besoin (non recommandé)
        DB::statement("ALTER TABLE reservations MODIFY date_reservation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;");
    }
};
