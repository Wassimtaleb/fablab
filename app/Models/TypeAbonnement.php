<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeAbonnement extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'prix',
        'duree_jours',
        'limite_reservations',
        'limite_heures'
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'duree_jours' => 'integer',
        'limite_reservations' => 'integer',
        'limite_heures' => 'integer'
    ];

    /**
     * Get the subscriptions for this type.
     */
    public function abonnements(): HasMany
    {
        return $this->hasMany(Abonnement::class);
    }
} 