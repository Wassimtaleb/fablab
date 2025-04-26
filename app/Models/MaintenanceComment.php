<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_id',
        'technicien_id',
        'contenu',
    ];

    /**
     * Relation avec la maintenance
     */
    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    /**
     * Relation avec le technicien
     */
    public function technicien()
    {
        return $this->belongsTo(Technicien::class);
    }
}
