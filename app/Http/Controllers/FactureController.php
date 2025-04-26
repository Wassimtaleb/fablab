<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PDFService;

class FactureController extends Controller
{
    /**
     * Génère une facture PDF pour un abonnement spécifique
     */
    public function generateFacture($id)
    {
        // Récupérer l'abonnement
        $abonnement = Abonnement::findOrFail($id);
        
        // Charger la réservation liée à l'abonnement
        $reservation = null;
        if (method_exists($abonnement, 'reservation')) {
            $reservation = $abonnement->reservation()->first();
        }
        
        // Vérifier que l'utilisateur est bien le propriétaire de l'abonnement
        if ($abonnement->user_id !== Auth::id()) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à accéder à cette facture.');
        }
        
        // Récupérer l'utilisateur
        $user = Auth::user();
        
        // Générer un numéro de facture unique
        $factureNum = 'FAC-' . date('Y') . '-' . str_pad($abonnement->id, 5, '0', STR_PAD_LEFT);
        
        // Créer les données pour la facture
        $data = [
            'facture_num' => $factureNum,
            'date_emission' => now()->format('d/m/Y'),
            'abonnement' => $abonnement,
            'user' => $user,
            'reservation' => $reservation,
        ];
        
        // Générer le PDF
        $pdfContent = PDFService::generatePDF('factures.template', $data);
        
        // Télécharger le PDF
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="facture_' . $factureNum . '.pdf"');
    }
}
