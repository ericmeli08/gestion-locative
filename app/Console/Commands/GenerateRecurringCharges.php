<?php

namespace App\Console\Commands;

use App\Models\ChargeMensuelle;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateRecurringCharges extends Command
{
    protected $signature = 'charges:generate-recurring';
    protected $description = 'Génère les charges mensuelles récurrentes.';

    public function handle()
    {
        // 1. On récupère UNIQUEMENT les charges principales récurrentes
        // où parent_charge_id est NULL.
        $recurringCharges = ChargeMensuelle::where('recurrent', true)
                                          ->whereNull('parent_charge_id')
                                          ->get(); 

        foreach ($recurringCharges as $charge) {
            // 2. On calcule la date de la prochaine charge
            // On peut aussi chercher la dernière charge enfant pour continuer la série
            $lastGeneratedCharge = $charge->recurrentCharges()->latest('date_paiement')->first() ?? $charge;
            $nextPaymentDate = Carbon::parse($lastGeneratedCharge->date_paiement)->addMonth();

            // 3. On vérifie si une charge a déjà été créée pour le mois en cours
            $existingCharge = ChargeMensuelle::where('parent_charge_id', $charge->id)
                ->whereDate('date_paiement', $nextPaymentDate)
                ->first();

            if (!$existingCharge) {
                // 4. On duplique la charge principale
                $newCharge = $charge->replicate();
                $newCharge->date_paiement = $nextPaymentDate;
                $newCharge->parent_charge_id = $charge->id; // Associez la nouvelle charge à son parent
                $newCharge->recurrent = true; // Gardez le statut récurrent pour la nouvelle charge
                $newCharge->save();

                $this->info("Charge récurrente '{$charge->service}' générée pour le {$nextPaymentDate->format('Y-m-d')}.");
            } else {
                $this->info("Charge '{$charge->service}' existe déjà pour ce mois. Skipping...");
            }
        }

        $this->info('Génération des charges récurrentes terminée.');
    }
}
