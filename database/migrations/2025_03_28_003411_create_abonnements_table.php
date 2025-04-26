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
        if (!Schema::hasTable('abonnements')) {
            Schema::create('abonnements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('type'); // mensuel ou annuel
                $table->decimal('prix', 8, 2);
                $table->datetime('date_debut')->nullable();
                $table->datetime('date_fin')->nullable();
                $table->boolean('actif')->default(false);
                $table->enum('statut', ['en_attente', 'approuve', 'refuse'])->default('en_attente');
                $table->text('message')->nullable();
                $table->timestamp('date_validation')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('abonnements', function (Blueprint $table) {
                if (!Schema::hasColumn('abonnements', 'type')) {
                    $table->string('type')->after('user_id');
                }
                if (!Schema::hasColumn('abonnements', 'prix')) {
                    $table->decimal('prix', 8, 2)->after('type');
                }
                if (!Schema::hasColumn('abonnements', 'date_debut')) {
                    $table->datetime('date_debut')->nullable()->after('prix');
                }
                if (!Schema::hasColumn('abonnements', 'date_fin')) {
                    $table->datetime('date_fin')->nullable()->after('date_debut');
                }
                if (!Schema::hasColumn('abonnements', 'actif')) {
                    $table->boolean('actif')->default(false)->after('date_fin');
                }
                if (!Schema::hasColumn('abonnements', 'statut')) {
                    $table->enum('statut', ['en_attente', 'approuve', 'refuse'])->default('en_attente')->after('actif');
                }
                if (!Schema::hasColumn('abonnements', 'message')) {
                    $table->text('message')->nullable()->after('statut');
                }
                if (!Schema::hasColumn('abonnements', 'date_validation')) {
                    $table->timestamp('date_validation')->nullable()->after('message');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
}; 