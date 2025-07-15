<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'element',
        'quantite_mois',
        'utilise',
        'reste',
        'seuil',
        'a_racheter',
        'appartement_id',
    ];

    protected $casts = [
        'a_racheter' => 'boolean',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }

    // Calcul automatique du reste et alerte de rachat
    public static function boot()
    {
        parent::boot();

        static::saving(function ($stock) {
            $stock->reste = $stock->quantite_mois - $stock->utilise;
            $stock->a_racheter = $stock->reste <= $stock->seuil;
        });
    }
}