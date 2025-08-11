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
        $selectedMonth = $request->get('selected_month');
        $showDaily = $request->get('show_daily', false);

        // Si période = 1 et on veut voir les détails journaliers
        if ($period == 1 && $showDaily && $selectedMonth) {
            return $this->getDailyData($selectedMonth, $apartmentId);
        }

        $startDate = Carbon::now()->subMonths($period)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        // Si un mois spécifique est sélectionné pour la période de 1 mois
        if ($period == 1 && $selectedMonth) {
            $startDate = Carbon::createFromFormat('Y-m', $selectedMonth)->startOfMonth();
            $endDate = Carbon::createFromFormat('Y-m', $selectedMonth)->endOfMonth();
        }

        // Générer les données mensuelles
        $monthlyData = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $monthData = $this->getMonthData($monthStart, $monthEnd, $apartmentId);
            $monthData['month'] = $current->format('Y-m');

            $monthlyData[] = $monthData;
            $current->addMonth();
        }

        // Trier par ordre chronologique
        usort($monthlyData, function($a, $b) {
            return strcmp($a['month'], $b['month']);
        });

        $apartments = Appartement::all();

        // Générer la liste des 12 derniers mois pour le sélecteur
        $availableMonths = [];
        $monthIterator = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $availableMonths[] = [
                'value' => $monthIterator->format('Y-m'),
                'label' => $monthIterator->format('F Y'),
                'label_fr' => $monthIterator->locale('fr')->format('F Y')
            ];
            $monthIterator->subMonth();
        }

        return Inertia::render('Reports/Monthly', [
            'monthlyData' => $monthlyData,
            'apartments' => $apartments,
            'selectedPeriod' => $period,
            'selectedApartment' => $apartmentId,
            'selectedMonth' => $selectedMonth,
            'showDaily' => $showDaily,
            'availableMonths' => $availableMonths,
            'isDaily' => false
        ]);
    }

    private function getDailyData($monthString, $apartmentId)
    {
        $month = Carbon::createFromFormat('Y-m', $monthString);
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $dailyData = [];
        $current = $startDate->copy();

        while ($current <= $endDate) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();

            $dayData = $this->getDayData($dayStart, $dayEnd, $apartmentId);
            $dayData['day'] = $current->format('Y-m-d');
            $dayData['day_name'] = $current->locale('fr')->translatedFormat('l j');

            $dailyData[] = $dayData;
            $current->addDay();
        }

        $apartments = Appartement::all();

        return Inertia::render('Reports/Monthly', [
            'dailyData' => $dailyData,
            'apartments' => $apartments,
            'selectedPeriod' => 1,
            'selectedApartment' => $apartmentId,
            'selectedMonth' => $monthString,
            'showDaily' => true,
            'isDaily' => true,
            'monthName' => $month->locale('fr')->format('F Y')
        ]);
    }

    private function getMonthData($start, $end, $apartmentId)
    {
        // Revenus du mois
        $revenueQuery = Reservation::where('date_entree', '>=', $start)
            ->where('date_entree', '<=', $end)
            ->where('statut_paiement', 'paid');

        if ($apartmentId) {
            $revenueQuery->where('appartement_id', $apartmentId);
        }

        $revenue = $revenueQuery->sum('revenus_totaux');

        // Dépenses du mois
        $expenseQuery = Depense::where('date', '>=', $start)
            ->where('date', '<=', $end);

        if ($apartmentId) {
            $expenseQuery->where('appartement_id', $apartmentId);
        }

        $expenses = $expenseQuery->sum('montant');

        // Charges du mois
        $chargeQuery = ChargeMensuelle::where('date_paiement', '>=', $start)
            ->where('date_paiement', '<=', $end);

        if ($apartmentId) {
            $chargeQuery->where('appartement_id', $apartmentId);
        }

        $charges = $chargeQuery->sum('montant');

        $profit = $revenue - $expenses - $charges;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'charges' => $charges,
            'profit' => $profit,
            'margin' => $margin
        ];
    }

    private function getDayData($start, $end, $apartmentId)
    {
        // Revenus du jour
        $revenueQuery = Reservation::where('date_entree', '>=', $start)
            ->where('date_entree', '<=', $end)
            ->where('statut_paiement', 'paid');

        if ($apartmentId) {
            $revenueQuery->where('appartement_id', $apartmentId);
        }

        $revenue = $revenueQuery->sum('revenus_totaux');

        // Dépenses du jour
        $expenseQuery = Depense::where('date', '>=', $start)
            ->where('date', '<=', $end);

        if ($apartmentId) {
            $expenseQuery->where('appartement_id', $apartmentId);
        }

        $expenses = $expenseQuery->sum('montant');

        // Charges du jour (proratisées)
        $charges = 0; // Les charges mensuelles ne sont généralement pas calculées par jour

        $profit = $revenue - $expenses - $charges;
        $margin = $revenue > 0 ? round(($profit / $revenue) * 100, 1) : 0;

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'charges' => $charges,
            'profit' => $profit,
            'margin' => $margin
        ];
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
