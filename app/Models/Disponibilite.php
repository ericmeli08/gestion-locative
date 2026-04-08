<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disponibilite extends Model
{
    use HasFactory;

    protected $fillable = [
        'appartement_id',
        'date',
        'statut',
        'reservation_id',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}