<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Depense;
use App\Models\ChargeMensuelle;
use App\Models\TauxOccupation;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $period = $request->get('period', 12);
        $apartmentId = $request->get('apartment');

        $startDate = Carbon::now()->subMonths($period)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Générer les données mensuelles
        $monthlyData = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            // Revenus du mois
            $revenueQuery = Reservation::where('date_entree', '>=', $monthStart)
                ->where('date_entree', '<=', $monthEnd)
                ->where('statut_paiement', 'paid');

            if ($apartmentId) {
                $revenueQuery->where('appartement_id', $apartmentId);
            }

            $revenue = $revenueQuery->sum('revenus_totaux');

            // Dépenses du mois
            $expenseQuery = Depense::where('date', '>=', $monthStart)
                ->where('date', '<=', $monthEnd);

            if ($apartmentId) {
                $expenseQuery->where('appartement_id', $apartmentId);
            }

            $expenses = $expenseQuery->sum('montant');

            // Charges du mois
            $chargeQuery = ChargeMensuelle::where('date_paiement', '>=', $monthStart)
                ->where('date_paiement', '<=', $monthEnd);

            if ($apartmentId) {
                $chargeQuery->where('appartement_id', $apartmentId);
            }

            $charges = $chargeQuery->sum('montant');

            $profit = $revenue - $expenses - $charges;
            $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

            $monthlyData[] = [
                'month' => $current->format('Y-m'),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'charges' => $charges,
                'profit' => $profit,
                'margin' => $margin
            ];

            $current->addMonth();
        }

        $apartments = Appartement::all();


        return Inertia::render('Reports/Monthly', [
            'monthlyData' => $monthlyData,
            'apartments' => $apartments,
            'selectedPeriod' => $period,
            'selectedApartment' => $apartmentId,
        ]);
    }

    public function occupancy(Request $request)
    {
        $period = $request->get('period', 12);
        $apartmentId = $request->get('apartment');

        $startDate = Carbon::now()->subMonths($period)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Calculer les taux d'occupation
        $occupancyData = [];
        $current = $startDate->copy();

        $apartments = $apartmentId ?
            Appartement::where('id', $apartmentId)->get() :
            Appartement::all();

        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();
            $daysInMonth = $monthEnd->day;

            foreach ($apartments as $apartment) {
                // Jours occupés dans le mois
                $occupiedDays = Reservation::where('appartement_id', $apartment->id)
                    ->where(function($query) use ($monthStart, $monthEnd) {
                        $query->whereBetween('date_entree', [$monthStart, $monthEnd])
                              ->orWhereBetween('date_sortie', [$monthStart, $monthEnd])
                              ->orWhere(function($q) use ($monthStart, $monthEnd) {
                                  $q->where('date_entree', '<=', $monthStart)
                                    ->where('date_sortie', '>=', $monthEnd);
                              });
                    })
                    ->sum('nombre_nuits');

                // Limiter aux jours du mois
                $occupiedDays = min($occupiedDays, $daysInMonth);

                // Revenus du mois pour cet appartement
                $revenue = Reservation::where('appartement_id', $apartment->id)
                    ->where('date_entree', '>=', $monthStart)
                    ->where('date_entree', '<=', $monthEnd)
                    ->where('statut_paiement', 'paid')
                    ->sum('revenus_totaux');

                $occupancyRate = $daysInMonth > 0 ? round(($occupiedDays / $daysInMonth) * 100, 1) : 0;

                $occupancyData[] = [
                    'month' => $current->format('Y-m'),
                    'apartment_id' => $apartment->id,
                    'apartment_name' => $apartment->name,
                    'available_days' => $daysInMonth,
                    'occupied_days' => $occupiedDays,
                    'occupancy_rate' => $occupancyRate,
                    'revenue' => $revenue
                ];
            }

            $current->addMonth();
        }

        return Inertia::render('Reports/Occupancy', [
            'occupancyData' => $occupancyData,
            'apartments' => Appartement::all(),
            'selectedPeriod' => $period,
            'selectedApartment' => $apartmentId,
        ]);
    }
}
