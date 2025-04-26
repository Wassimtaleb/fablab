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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropColumn([
                'type',
                'prix',
                'date_debut',
                'date_fin',
                'actif',
                'statut',
                'message',
                'date_validation'
            ]);
        });
    }
};
