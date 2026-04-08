<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TauxOccupation extends Model
{
    use HasFactory;

    protected $fillable = [
        'mois',
        'appartement_id',
        'jours_disponibles',
        'jours_loues',
        'taux_occupation',
    ];

    protected $casts = [
        'mois' => 'date',
        'taux_occupation' => 'decimal:2',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }

    // Calcul automatique du taux d'occupation
    public static function boot()
    {
        parent::boot();

        static::saving(function ($taux) {
            if ($taux->jours_disponibles > 0) {
                $taux->taux_occupation = ($taux->jours_loues / $taux->jours_disponibles) * 100;
            }
        });
    }
}