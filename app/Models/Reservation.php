<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['date_entree', 'date_sortie', 'client', 'email', 'telephone', 'plateforme', 'appartement_id', 'prix_nuit', 'nombre_nuits', 'revenus_totaux', 'date_paiement', 'statut_paiement', 'notes'];

    protected $casts = [
        'date_entree' => 'datetime',
        'date_sortie' => 'datetime',
        'date_paiement' => 'datetime',
        'prix_nuit' => 'decimal:2',
        'revenus_totaux' => 'decimal:2',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    // Calcul automatique du nombre de nuits
    public static function boot()
    {
        parent::boot();

        static::saving(function ($reservation) {
            if ($reservation->date_entree && $reservation->date_sortie) {
                $dateEntree = Carbon::parse($reservation->date_entree);
                $dateSortie = Carbon::parse($reservation->date_sortie);
                $reservation->nombre_nuits = $dateEntree->diffInDays($dateSortie);
                $reservation->revenus_totaux = $reservation->nombre_nuits * $reservation->prix_nuit;
            }
        });
    }
}
