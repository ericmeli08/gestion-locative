<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type_depense',
        'description',
        'appartement_id',
        'montant',
        'facture_path',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }
}