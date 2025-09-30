<?php

namespace App\Services;

use App\Models\ChargeMensuelle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecurringChargeService
{
    public function generateMissingCharges(): void
    {
        $currentDate = Carbon::now();

        // On définit le mois pour lequel on doit s'assurer que les charges existent (le mois actuel)
        $targetMonth = $currentDate->startOfMonth();

        // 1. Vérifier si les charges récurrentes ont DÉJÀ été générées pour ce mois
        // On va vérifier si une récurrence a été créée pour le mois ciblé.
        $alreadyGenerated = ChargeMensuelle::whereNull('parent_charge_id') // On prend une charge maîtresse au hasard
            ->where('recurrent', true)
            ->whereHas('recurrentCharges', function ($query) use ($targetMonth) {
                // On vérifie s'il existe une récurrence enfant pour la date cible
                $query->whereYear('date_paiement', $targetMonth->year)
                      ->whereMonth('date_paiement', $targetMonth->month);
            })
            ->exists();

        // Si des charges ont déjà été générées pour ce mois, on s'arrête.
        if ($alreadyGenerated) {
            return;
        }

        // Si elles n'existent pas, on lance la génération.
        $this->executeGeneration();
    }

    public function executeGeneration()
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

                Log::info("Charge récurrente '{$charge->service}' générée pour le {$nextPaymentDate->format('Y-m-d')}.");

            }
            else {
                Log::info("Charge '{$charge->service}' existe déjà pour ce mois. Skipping...");
            }
        }

        Log::info('Génération des charges récurrentes terminée.');
    }
}
