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
        Schema::table('reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('reservations', 'date_reservation')) {
                $table->datetime('date_reservation')->nullable();
            }
            if (!Schema::hasColumn('reservations', 'heure_debut')) {
                $table->time('heure_debut')->nullable();
            }
            if (!Schema::hasColumn('reservations', 'duree')) {
                $table->integer('duree')->nullable();
            }
            if (!Schema::hasColumn('reservations', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('reservations', 'statut')) {
                $table->string('statut')->default('en_attente');
            }
            if (!Schema::hasColumn('reservations', 'date_validation')) {
                $table->datetime('date_validation')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['date_reservation', 'heure_debut', 'duree', 'description', 'statut', 'date_validation']);
        });
    }
};
