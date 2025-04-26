<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Corrige l'énum pour accepter les statuts corrects
        DB::statement("ALTER TABLE reservations MODIFY statut ENUM('en_attente', 'validee', 'refusee') DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remet l'ancien enum (adapter si besoin)
        DB::statement("ALTER TABLE reservations MODIFY statut ENUM('en_attente', 'approuve', 'refuse') DEFAULT 'en_attente'");
    }
};
