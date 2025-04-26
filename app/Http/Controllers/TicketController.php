<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PDFService;

class TicketController extends Controller
{
    /**
     * Génère un ticket PDF pour une réservation spécifique
     */
    public function generateTicket($id)
    {
        // Récupérer la réservation
        $reservation = Reservation::with(['machine', 'user'])
            ->findOrFail($id);
        
        // Vérifier que l'utilisateur est bien le propriétaire de la réservation
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->route('user.reservations.my')
                ->with('error', 'Vous n\'êtes pas autorisé à accéder à ce ticket.');
        }
        
        // Vérifier que la réservation est validée
        if ($reservation->statut !== 'validee') {
            return redirect()->route('user.reservations.my')
                ->with('error', 'Seules les réservations validées peuvent générer un ticket.');
        }
        
        // Récupérer l'utilisateur
        $user = Auth::user();
        
        // Générer un numéro de ticket unique
        $ticketNum = 'TICK-' . date('Y') . '-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT);
        
        // Créer les données pour le ticket
        $data = [
            'ticket_num' => $ticketNum,
            'date_emission' => now()->format('d/m/Y'),
            'reservation' => $reservation,
            'user' => $user,
        ];
        
        // Générer le PDF
        $pdfContent = PDFService::generatePDF('tickets.template', $data);
        
        // Télécharger le PDF
        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="ticket_' . $ticketNum . '.pdf"');
    }
}
