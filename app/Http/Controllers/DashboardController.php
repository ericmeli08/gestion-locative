<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Depense;
use App\Models\Stock;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        // Calcul des métriques
        $currentRevenue = Reservation::where('date_entree', '>=', $currentMonth)
            ->where('statut_paiement', 'paid')
            ->sum('revenus_totaux');

        $previousRevenue = Reservation::where('date_entree', '>=', $previousMonth)
            ->where('date_entree', '<', $currentMonth)
            ->where('statut_paiement', 'paid')
            ->sum('revenus_totaux');

        $currentExpenses = Depense::where('date', '>=', $currentMonth)->sum('montant');
        $previousExpenses = Depense::where('date', '>=', $previousMonth)
            ->where('date', '<', $currentMonth)
            ->sum('montant');

        $profit = $currentRevenue - $currentExpenses;
        $previousProfit = $previousRevenue - $previousExpenses;

        // Calcul du taux d'occupation
        $totalDaysInMonth = $currentMonth->daysInMonth;
        $occupiedDays = Reservation::where('date_entree', '<=', Carbon::now()->endOfMonth())
            ->where('date_sortie', '>=', $currentMonth)
            ->sum('nombre_nuits');

        $occupancyRate = $totalDaysInMonth > 0 ? round(($occupiedDays / $totalDaysInMonth) * 100, 1) : 0;

        // Taux d'occupation du mois précédent
        $previousMonthDays = $previousMonth->daysInMonth;
        $previousOccupiedDays = Reservation::where('date_entree', '<=', $previousMonth->copy()->endOfMonth())
            ->where('date_sortie', '>=', $previousMonth)
            ->where('date_entree', '<', $currentMonth)
            ->sum('nombre_nuits');

        $previousOccupancyRate = $previousMonthDays > 0 ? round(($previousOccupiedDays / $previousMonthDays) * 100, 1) : 0;

        // Réservations récentes
        $recentReservations = Reservation::with('appartement')
            ->latest()
            ->take(5)
            ->get();

        // Stocks faibles
        $lowStocks = Stock::whereRaw('reste <= seuil')
            ->with('appartement')
            ->get();

        return Inertia::render('Dashboard', [
            'metrics' => [
                'revenue' => (float) $currentRevenue,
                'revenueTrend' => $this->calculateTrend($currentRevenue, $previousRevenue),
                'expenses' => (float) $currentExpenses,
                'expensesTrend' => $this->calculateTrend($currentExpenses, $previousExpenses),
                'profit' => (float) $profit,
                'profitTrend' => $this->calculateTrend($profit, $previousProfit),
                'occupancyRate' => (float) $occupancyRate,
                'occupancyTrend' => $this->calculateTrend($occupancyRate, $previousOccupancyRate),
            ],
            'recentReservations' => $recentReservations,
            'lowStocks' => $lowStocks,
        ]);
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? '+100%' : '0%';
        }

        $percentage = (($current - $previous) / $previous) * 100;
        return ($percentage >= 0 ? '+' : '') . number_format($percentage, 1, '.', '') . '%';
    }
}
