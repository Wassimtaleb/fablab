<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Abonnement;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeleteExpiredAbonnements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'abonnements:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supprimer automatiquement les abonnements expirés';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $expiredAbonnements = Abonnement::where('statut', 'actif')
            ->where('date_fin', '<', $now)
            ->get();

        $count = $expiredAbonnements->count();
        foreach ($expiredAbonnements as $abonnement) {
            $abonnement->delete();
            Log::info('Abonnement expiré supprimé automatiquement', [
                'abonnement_id' => $abonnement->id,
                'user_id' => $abonnement->user_id,
                'date_fin' => $abonnement->date_fin,
            ]);
        }

        $this->info("{$count} abonnement(s) expiré(s) supprimé(s) automatiquement.");
    }
}
