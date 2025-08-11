<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Depense::with('appartement');

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('type_depense', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type_depense', $request->type);
        }

        if ($request->filled('apartment')) {
            $query->where('appartement_id', $request->apartment);
        }

        $depenses = $query->latest('date')->paginate(15)->withQueryString();
        $apartments = Appartement::all();

        return Inertia::render('Depenses/Index', [
            'depenses' => $depenses,
            'apartments' => $apartments,
            'filters' => $request->only(['search', 'type', 'apartment']),
        ]);
    }

    public function create()
    {
        $apartments = Appartement::all();

        return Inertia::render('Depenses/Form', [
            'apartments' => $apartments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type_depense' => 'required|string|max:255',
            'description' => 'required|string',
            'appartement_id' => 'nullable|exists:appartements,id',
            'montant' => 'required|numeric|min:0',
        ]);

        Depense::create($validated);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense créée avec succès.');
    }

    public function edit(Depense $depense)
    {
        $apartments = Appartement::all();

        return Inertia::render('Depenses/Form', [
            'depense' => $depense,
            'apartments' => $apartments,
        ]);
    }

    public function update(Request $request, Depense $depense)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type_depense' => 'required|string|max:255',
            'description' => 'required|string',
            'appartement_id' => 'nullable|exists:appartements,id',
            'montant' => 'required|numeric|min:0',
        ]);

        $depense->update($validated);

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy(Depense $depense)
    {
        $depense->delete();

        return redirect()->route('depenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }
}