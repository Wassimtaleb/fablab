<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
           
           
        });
    }

    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn([
                'expediteur_type',
                'expediteur_id',
                'destinataire_type',
                'destinataire_id',
                'sujet',
                'contenu',
                'lu'
            ]);
        });
    }
}; 