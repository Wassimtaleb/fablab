<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Machine extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'statut',
        'date_maintenance',
        'disponible',
        'image'
    ];

    protected $casts = [
        'date_maintenance' => 'datetime',
        'disponible' => 'boolean'
    ];

    public function getStatutAttribute($value)
    {
        Log::info('Vérification du statut de la machine', [
            'machine_id' => $this->id,
            'machine_nom' => $this->nom,
            'statut_initial' => $value,
            'disponible' => $this->disponible,
            'reservations' => $this->reservations()->where('statut', 'validée')
                ->where('date_reservation', '>=', now())
                ->get()
        ]);

        if ($value === 'en_maintenance' || $value === 'hors_service') {
            return $value;
        }

        if (!$this->disponible) {
            return 'non_disponible';
        }

        return $value;
    }

    public function isAvailable()
    {
        Log::info('Vérification de la disponibilité de la machine', [
            'machine_id' => $this->id,
            'machine_nom' => $this->nom,
            'disponible' => $this->disponible,
            'statut' => $this->statut
        ]);

        return $this->disponible && $this->statut !== 'non_disponible';
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
} 