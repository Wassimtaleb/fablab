<?php

namespace App\Services;

use Illuminate\Support\Facades\View;

class PDFService
{
    /**
     * Génère un PDF à partir d'une vue
     *
     * @param string $view
     * @param array $data
     * @return string
     */
    public static function generatePDF($view, $data = [])
    {
        // Charger la vue avec les données
        $html = View::make($view, $data)->render();
        
        // Créer une instance de Dompdf
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        
        // Définir le format du papier et l'orientation
        $dompdf->setPaper('A4', 'portrait');
        
        // Rendre le HTML en PDF
        $dompdf->render();
        
        // Obtenir le contenu du PDF
        return $dompdf->output();
    }
}
