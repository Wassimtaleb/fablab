<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'technicien_id',
        'type',
        'description',
        'date_debut',
        'date_fin',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    /**
     * Les statuts possibles pour une maintenance
     */
    const STATUT_PLANIFIEE = 'planifiee';
    const STATUT_EN_COURS = 'en_cours';
    const STATUT_TERMINEE = 'terminee';
    const STATUT_ANNULEE = 'annulee';

    /**
     * Relation avec la machine
     */
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    /**
     * Relation avec le technicien
     */
    public function technicien()
    {
        return $this->belongsTo(Technicien::class);
    }

    /**
     * Relation avec les commentaires
     */
    public function commentaires()
    {
        return $this->hasMany(MaintenanceComment::class);
    }

    /**
     * Vérifie si la maintenance est en cours ou planifiée
     */
    public function estActive()
    {
        return in_array($this->statut, [self::STATUT_PLANIFIEE, self::STATUT_EN_COURS]);
    }

    /**
     * Vérifie si la maintenance est terminée
     */
    public function estTerminee()
    {
        return $this->statut === self::STATUT_TERMINEE;
    }

    /**
     * Vérifie si la maintenance est annulée
     */
    public function estAnnulee()
    {
        return $this->statut === self::STATUT_ANNULEE;
    }

    /**
     * Vérifie si une réservation est en conflit avec cette maintenance
     */
    public function estEnConflitAvecReservation($dateReservation, $heureDebut, $duree)
    {
        // Si la maintenance est terminée ou annulée, pas de conflit
        if (!$this->estActive()) {
            return false;
        }

        // Convertir l'heure de début en minutes depuis minuit
        list($heures, $minutes) = explode(':', $heureDebut);
        $debutReservationMinutes = $heures * 60 + $minutes;
        $finReservationMinutes = $debutReservationMinutes + $duree;

        // Si la réservation est pour un autre jour que la maintenance, pas de conflit
        if ($dateReservation->format('Y-m-d') !== $this->date_debut->format('Y-m-d')) {
            return false;
        }

        // Convertir l'heure de début de la maintenance en minutes depuis minuit
        $debutMaintenanceMinutes = $this->date_debut->format('H') * 60 + $this->date_debut->format('i');
        
        // Si la maintenance n'a pas de date de fin, on considère qu'elle dure 2 heures
        if ($this->date_fin) {
            $finMaintenanceMinutes = $this->date_fin->format('H') * 60 + $this->date_fin->format('i');
        } else {
            $finMaintenanceMinutes = $debutMaintenanceMinutes + 120; // 2 heures par défaut
        }

        // Vérifier le chevauchement
        return max($debutReservationMinutes, $debutMaintenanceMinutes) < min($finReservationMinutes, $finMaintenanceMinutes);
    }
}