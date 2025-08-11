<?php

namespace App\Http\Controllers;

use App\Models\DegatReparation;
use App\Models\Appartement;
use App\Models\Depense;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DegatReparationController extends Controller
{
    public function index(Request $request)
    {
        $query = DegatReparation::with('appartement');

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%')->orWhere('type_degat', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('statut', $request->status);
        }

        if ($request->filled('apartment')) {
            $query->where('appartement_id', $request->apartment);
        }

        $degats = $query->latest('date')->paginate(15)->withQueryString();
        $apartments = Appartement::all();

        return Inertia::render('DegatsReparations/Index', [
            'degats' => $degats,
            'apartments' => $apartments,
            'filters' => $request->only(['search', 'status', 'apartment']),
        ]);
    }

    public function create()
    {
        $apartments = Appartement::all();

        return Inertia::render('DegatsReparations/Form', [
            'apartments' => $apartments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'appartement_id' => 'required|exists:appartements,id',
            'type_degat' => 'required|string|max:255',
            'description' => 'required|string',
            'solution' => 'nullable|string',
            'cout' => 'nullable|numeric|min:0',
            'statut' => 'required|in:signale,en_cours,termine',
            'date_reparation' => 'nullable|date',
            'reparateur' => 'nullable|string|max:255',
        ]);

        $degat = DegatReparation::create($validated);

        // Si un coût est renseigné, on crée une dépense
        if (!empty($degat->cout)) {
            Depense::create([
                'date' => $degat->date_reparation ?? $degat->date,
                'type_depense' => 'Réparation dégât : ' . $degat->type_degat,
                'description' => 'Dégât ID:' . $degat->id . ' - ' . $degat->description . ($degat->solution ? ' | Solution: ' . $degat->solution : ''),
                'appartement_id' => $degat->appartement_id,
                'montant' => $degat->cout,
            ]);
        }

        return redirect()->route('degats.index')->with('success', 'Dégât/Réparation créé avec succès.');
    }

    public function edit(DegatReparation $degat)
    {
        $apartments = Appartement::all();

        return Inertia::render('DegatsReparations/Form', [
            'degat' => $degat,
            'apartments' => $apartments,
        ]);
    }

    public function update(Request $request, DegatReparation $degat)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'appartement_id' => 'required|exists:appartements,id',
            'type_degat' => 'required|string|max:255',
            'description' => 'required|string',
            'solution' => 'nullable|string',
            'cout' => 'nullable|numeric|min:0',
            'statut' => 'required|in:signale,en_cours,termine',
            'date_reparation' => 'nullable|date',
            'reparateur' => 'nullable|string|max:255',
        ]);

        $degat->update($validated);

        // Si un cout est présent, on gère la dépense
        if (!empty($validated['cout'])) {
            // On cherche une dépense déjà liée à ce dégât
            $depense = Depense::where('description', 'like', '%Dégât ID:' . $degat->id . '%')->first();

            if ($depense) {
                // Mise à jour de la dépense existante
                $depense->update([
                    'date' => $degat->date_reparation ?? $degat->date,
                    'type_depense' => 'Réparation dégât : ' . $degat->type_degat,
                    'description' => 'Dégât ID:' . $degat->id . ' - ' . $degat->description . ($degat->solution ? ' | Solution: ' . $degat->solution : ''),
                    'appartement_id' => $degat->appartement_id,
                    'montant' => $degat->cout,
                ]);
            } else {
                // Création de la dépense si elle n'existe pas
                Depense::create([
                    'date' => $degat->date_reparation ?? $degat->date,
                    'type_depense' => 'Réparation dégât : ' . $degat->type_degat,
                    'description' => 'Dégât ID:' . $degat->id . ' - ' . $degat->description . ($degat->solution ? ' | Solution: ' . $degat->solution : ''),
                    'appartement_id' => $degat->appartement_id,
                    'montant' => $degat->cout,
                ]);
            }
        }

        return redirect()->route('degats.index')->with('success', 'Dégât/Réparation mis à jour avec succès.');
    }

    public function destroy(DegatReparation $degat)
    {
        $degat->delete();

        return redirect()->route('degats.index')->with('success', 'Dégât/Réparation supprimé avec succès.');
    }
}
