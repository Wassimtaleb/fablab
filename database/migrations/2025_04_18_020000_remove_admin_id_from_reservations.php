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
        // Supprimer la clé étrangère si elle existe (ignore l'erreur si absente)
        try {
            DB::statement('ALTER TABLE reservations DROP FOREIGN KEY reservations_admin_id_foreign');
        } catch (\Exception $e) {
            // Ignore l'erreur si la clé n'existe pas
        }
        // Supprimer la colonne si elle existe
        if (Schema::hasColumn('reservations', 'admin_id')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->dropColumn('admin_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id')->nullable()->after('date_validation');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }
};
