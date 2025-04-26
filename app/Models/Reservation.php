<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'machine_id',
        'date_reservation',
        'heure_debut',
        'duree',
        'description',
        'statut',
        'date_validation'
    ];

    protected $casts = [
        'date_reservation' => 'datetime',
        'date_validation' => 'datetime',
        'duree' => 'integer'
    ];

    protected $attributes = [
        'statut' => 'en_attente'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function isEnAttente(): bool
    {
        return $this->statut === 'en_attente';
    }
}