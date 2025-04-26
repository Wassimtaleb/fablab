<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Abonnement extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'prix',
        'date_debut',
        'date_fin',
        'actif',
        'statut',
        'message',
        'date_validation'
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'actif' => 'boolean',
        'date_validation' => 'datetime',
        'prix' => 'decimal:2'
    ];

    protected $attributes = [
        'actif' => false,
        'statut' => 'en_attente'
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type of subscription.
     */
    public function typeAbonnement(): BelongsTo
    {
        return $this->belongsTo(TypeAbonnement::class);
    }

    /**
     * Relation avec la réservation liée à l'abonnement
     */
    public function reservation(): HasOne
    {
        return $this->hasOne(Reservation::class, 'abonnement_id');
    }

    /**
     * Set the type and calculate the price and end date.
     */
    public function setTypeAttribute($value)
    {
        $this->attributes['type'] = $value;
        $this->attributes['prix'] = $value === 'mensuel' ? 29.99 : 299.99;
        $this->attributes['date_debut'] = now();
        $this->attributes['date_fin'] = $value === 'mensuel' ? now()->addMonth() : now()->addYear();
    }
} 