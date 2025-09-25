<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChargeMensuelle extends Model
{
    use HasFactory;

    protected $fillable = [
        'service',
        'appartement_id',
        'date_paiement',
        'montant',
        'recurrent',
        'notes',
        'parent_charge_id',
    ];

    protected $casts = [
        'date_paiement' => 'date',
        'montant' => 'decimal:2',
        'recurrent' => 'boolean',
    ];

    public function appartement()
    {
        return $this->belongsTo(Appartement::class);
    }

    // Définir la relation "parent-enfant"
    public function recurrentCharges()
    {
        return $this->hasMany(ChargeMensuelle::class, 'parent_charge_id');
    }

    //  relation vers le parent
    public function parentCharge()
    {
        return $this->belongsTo(ChargeMensuelle::class, 'parent_charge_id');
    }
}