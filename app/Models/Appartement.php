<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appartement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'address',
        'capacity',
        'surface',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'surface' => 'decimal:2',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    public function chargesMensuelles()
    {
        return $this->hasMany(ChargeMensuelle::class);
    }

    public function tauxOccupations()
    {
        return $this->hasMany(TauxOccupation::class);
    }

    public function disponibilites()
    {
        return $this->hasMany(Disponibilite::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function degatReparations()
    {
        return $this->hasMany(DegatReparation::class);
    }
}