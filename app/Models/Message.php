<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'expediteur_id',
        'expediteur_type',
        'destinataire_id',
        'destinataire_type',
        'sujet',
        'contenu',
        'lu',
    ];

    protected $casts = [
        'lu' => 'boolean',
    ];

    // Relation avec l'expéditeur (polymorphique)
    public function expediteur()
    {
        return $this->morphTo();
    }

    // Relation avec le destinataire (polymorphique)
    public function destinataire()
    {
        return $this->morphTo();
    }
} 