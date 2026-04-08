<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeMensuel extends Model
{
    use HasFactory;

    protected $fillable = [
        'mois',
        'revenus_totaux',
        'depenses_totales',
        'charges_totales',
        'benefice_net',
    ];

    protected $casts = [
        'mois' => 'date',
        'revenus_totaux' => 'decimal:2',
        'depenses_totales' => 'decimal:2',
        'charges_totales' => 'decimal:2',
        'benefice_net' => 'decimal:2',
    ];

    // Calcul automatique du bénéfice net
    public static function boot()
    {
        parent::boot();

        static::saving(function ($resume) {
            $resume->benefice_net = $resume->revenus_totaux - $resume->depenses_totales - $resume->charges_totales;
        });
    }
}