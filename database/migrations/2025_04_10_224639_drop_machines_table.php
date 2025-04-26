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
        }
   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description');
            $table->string('statut')->default('disponible');
            $table->timestamp('date_maintenance')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();
        });
    }
};
