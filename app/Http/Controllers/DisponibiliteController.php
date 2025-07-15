<?php

namespace App\Http\Controllers;

use App\Models\Disponibilite;
use App\Models\Appartement;
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

        return Inertia::render('Disponibilites/Index', [
            'apartments' => $apartments,
            'disponibilites' => $disponibilites,
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