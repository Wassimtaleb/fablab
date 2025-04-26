<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->onDelete('cascade');
            $table->foreignId('technicien_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->text('description');
            $table->dateTime('date_debut');
            $table->dateTime('date_fin')->nullable();
            $table->string('statut')->default('en_cours');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenances');
    }
}; 