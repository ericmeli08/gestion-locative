<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegatReparation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'appartement_id',
        'type_degat',
        'description',
        'solution',
        'cout',
        'statut',
        'date_reparation',
        'reparateur',
    ];

    protected $casts = [
        'date' => 'date',
        'date_reparation' => 'date',
        'cout' => 'decimal:2',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }
}