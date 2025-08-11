<?php

namespace App\Http\Controllers;

use App\Models\Disponibilite;
use App\Models\Appartement;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DisponibiliteController extends Controller
{
    public function index(Request $request)
    {
        $apartments = Appartement::all();
        $currentMonth = $request->get('month', Carbon::now()->format('Y-m'));

        $startDate = Carbon::parse($currentMonth . '-01');
        $endDate = $startDate->copy()->endOfMonth();

        $disponibilites = Disponibilite::with(['appartement', 'reservation'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('appartement_id');

    // On récupère les bornes du currentMonth (début et fin)
    $startOfMonth = Carbon::parse($currentMonth)->startOfMonth()->startOfDay();
    $endOfMonth = Carbon::parse($currentMonth)->endOfMonth()->endOfDay();

    // On récupère les réservations qui chevauchent ce mois
    $reservations = Reservation::with('appartement')->where(function ($query) use ($startOfMonth, $endOfMonth) {
        $query->whereBetween('date_entree', [$startOfMonth, $endOfMonth])
              ->orWhereBetween('date_sortie', [$startOfMonth, $endOfMonth])
              ->orWhere(function ($q) use ($startOfMonth, $endOfMonth) {
                  $q->where('date_entree', '<=', $startOfMonth)
                    ->where('date_sortie', '>=', $endOfMonth);
              });
    })->get();

    // On transforme chaque réservation en plusieurs lignes par jour
    $result = [];

    foreach ($reservations as $reservation) {
        $start = Carbon::parse((string) $reservation->date_entree);
        $end = Carbon::parse((string) $reservation->date_sortie);

        // On ne garde que les jours dans le mois demandé
        $current = $start->copy();
        while ($current <= $end) {
            if ($current->between($startOfMonth, $endOfMonth)) {
                $result[] = [
                    'reservation' => $reservation,
                    'date' => $current->format('Y-m-d'),
                ];
            }
            $current->addDay();
        }
    }

        return Inertia::render('Disponibilites/Index', [
            'apartments' => $apartments,
            'disponibilites' => $disponibilites,
            'available' => $result,
            'currentMonth' => $currentMonth,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'appartement_id' => 'required|exists:appartements,id',
            'date' => 'required|date',
            'statut' => 'required|in:available,occupied,maintenance',
            'notes' => 'nullable|string',
        ]);

        Disponibilite::updateOrCreate(
            [
                'appartement_id' => $validated['appartement_id'],
                'date' => $validated['date']
            ],
            $validated
        );

        return back()->with('success', 'Disponibilité mise à jour avec succès.');
    }

    public function update(Request $request, Disponibilite $disponibilite)
    {
        $validated = $request->validate([
            'statut' => 'required|in:available,occupied,maintenance',
            'notes' => 'nullable|string',
        ]);

        $disponibilite->update($validated);

        return back()->with('success', 'Disponibilité mise à jour avec succès.');
    }
}