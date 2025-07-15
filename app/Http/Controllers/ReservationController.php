<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Appartement;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservation::with('appartement'); 

        // Filtres
        if ($request->filled('search')) {
            $query->where('client', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('statut_paiement', $request->status);
        }

        if ($request->filled('apartment')) {
            $query->where('appartement_id', $request->apartment);
        }

        $reservations = $query->latest()->paginate(10)->withQueryString();
        $apartments = Appartement::all();

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
            'apartments' => $apartments,
            'filters' => $request->only(['search', 'status', 'apartment']),
        ]);
    }

    public function create()
    {
        $apartments = Appartement::all();

        return Inertia::render('Reservations/Form', [
            'apartments' => $apartments,
        ]);
    }

    public function store(StoreReservationRequest $request)
    {
        $validated = $request->validated();
        
        // Calcul automatique du nombre de nuits et revenus
        $dateEntree = \Carbon\Carbon::parse($validated['date_entree']);
        $dateSortie = \Carbon\Carbon::parse($validated['date_sortie']);
        $nombreNuits = $dateEntree->diffInDays($dateSortie);
        
        $validated['nombre_nuits'] = $nombreNuits;
        $validated['revenus_totaux'] = $nombreNuits * $validated['prix_nuit'];

        Reservation::create($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation créée avec succès.');
    }

    public function show(Reservation $reservation)
    {
        $reservation->load('appartement');
        
        return Inertia::render('Reservations/Show', [
            'reservation' => $reservation,
        ]);
    }

    public function edit(Reservation $reservation)
    {
        $apartments = Appartement::all();

        return Inertia::render('Reservations/Form', [
            'reservation' => $reservation,
            'apartments' => $apartments,
        ]);
    }

    public function update(UpdateReservationRequest $request, Reservation $reservation)
    {
        $validated = $request->validated();
        
        // Recalcul automatique
        $dateEntree = \Carbon\Carbon::parse($validated['date_entree']);
        $dateSortie = \Carbon\Carbon::parse($validated['date_sortie']);
        $nombreNuits = $dateEntree->diffInDays($dateSortie);
        
        $validated['nombre_nuits'] = $nombreNuits;
        $validated['revenus_totaux'] = $nombreNuits * $validated['prix_nuit'];

        $reservation->update($validated);

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation mise à jour avec succès.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Réservation supprimée avec succès.');
    }
}