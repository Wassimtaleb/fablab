<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer les anciennes colonnes
            $table->dropColumn(['date_souhaitee', 'date_validee', 'message_utilisateur', 'message_admin']);
            
            // Ajouter les nouvelles colonnes
            $table->date('date_reservation')->after('machine_id');
            $table->time('heure_debut')->after('date_reservation');
            $table->integer('duree')->after('heure_debut');
            $table->text('description')->nullable()->after('duree');
            $table->timestamp('date_validation')->nullable()->after('statut');
            $table->unsignedBigInteger('admin_id')->nullable()->after('date_validation');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Supprimer les nouvelles colonnes
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['admin_id']);
            $table->dropColumn(['date_reservation', 'heure_debut', 'duree', 'description', 'date_validation']);
            
            // Restaurer les anciennes colonnes
            $table->timestamp('date_souhaitee')->nullable();
            $table->timestamp('date_validee')->nullable();
            $table->text('message_utilisateur')->nullable();
            $table->text('message_admin')->nullable();
        });
    }
}; 