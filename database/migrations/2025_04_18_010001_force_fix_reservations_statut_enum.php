<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Forcer la correction des valeurs invalides avant de modifier l'ENUM
        DB::statement("UPDATE reservations SET statut = 'en_attente' WHERE statut NOT IN ('en_attente', 'validee', 'refusee')");
        DB::statement("ALTER TABLE reservations MODIFY statut ENUM('en_attente', 'validee', 'refusee') DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE reservations MODIFY statut ENUM('en_attente', 'approuve', 'refuse') DEFAULT 'en_attente'");
    }
};
