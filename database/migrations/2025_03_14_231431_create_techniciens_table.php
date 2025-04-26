<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_create_techniciens_table.php
   public function up()
   {
       if (!Schema::hasTable('techniciens')) {
           Schema::create('techniciens', function (Blueprint $table) {
               $table->id();
               $table->string('nom');
               $table->string('prenom');
               $table->string('email')->unique();
               $table->string('password');
               $table->timestamps();
           });
       }
   }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('techniciens');
    }
};
