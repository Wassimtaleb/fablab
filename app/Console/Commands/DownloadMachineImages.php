<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Machine;

class DownloadMachineImages extends Command
{
    protected $signature = 'machines:download-images';
    protected $description = 'Download and save machine images';

    private $machineImages = [
        [
            'nom' => '3D Printer',
            'description' => 'Imprimante 3D professionnelle pour prototypage rapide',
            'image_url' => 'https://raw.githubusercontent.com/codeium/fablab-images/main/3d-printer.jpg',
            'statut' => 'disponible',
            'disponible' => true
        ],
        [
            'nom' => 'Laser Cutter',
            'description' => 'Découpeuse laser de précision',
            'image_url' => 'https://raw.githubusercontent.com/codeium/fablab-images/main/laser-cutter.jpg',
            'statut' => 'disponible',
            'disponible' => true
        ],
        [
            'nom' => 'CNC Router',
            'description' => 'Machine CNC pour usinage de précision',
            'image_url' => 'https://raw.githubusercontent.com/codeium/fablab-images/main/cnc-router.jpg',
            'statut' => 'disponible',
            'disponible' => true
        ]
    ];

    public function handle()
    {
        // Créer le dossier de stockage s'il n'existe pas
        if (!Storage::exists('public/machines')) {
            Storage::makeDirectory('public/machines');
        }

        // Télécharger et sauvegarder les images de démonstration
        $defaultImages = [
            'https://images.unsplash.com/photo-1615603699504-3c78e3756c8e?w=800', // Imprimante 3D
            'https://images.unsplash.com/photo-1581091226825-c6a89e7e4801?w=800', // Découpeuse laser
            'https://images.unsplash.com/photo-1567789884554-0b844b6af3e4?w=800'  // CNC
        ];

        foreach ($defaultImages as $index => $imageUrl) {
            try {
                $response = Http::withoutVerifying()->get($imageUrl);
                
                if ($response->successful()) {
                    $filename = time() . '_machine_' . ($index + 1) . '.jpg';
                    Storage::put('public/machines/' . $filename, $response->body());
                    
                    // Créer ou mettre à jour la machine correspondante
                    $machineData = $this->machineImages[$index];
                    Machine::updateOrCreate(
                        ['nom' => $machineData['nom']],
                        [
                            'description' => $machineData['description'],
                            'statut' => $machineData['statut'],
                            'disponible' => $machineData['disponible'],
                            'image' => $filename
                        ]
                    );
                    
                    $this->info("Image téléchargée avec succès : " . $filename);
                }
            } catch (\Exception $e) {
                $this->error("Erreur lors du téléchargement de l'image " . ($index + 1) . ": " . $e->getMessage());
            }
        }
    }
}
