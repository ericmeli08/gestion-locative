<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            'is_general_expense' => 'boolean',
            'selected_apartments' => 'array',
            'selected_apartments.*' => 'exists:appartements,id',
        ]);

        // Validation personnalisée pour les dépenses générales
        if ($validated['is_general_expense'] && empty($validated['selected_apartments'])) {
            return back()->withErrors([
                'selected_apartments' => 'Veuillez sélectionner au moins un appartement pour une dépense générale.'
            ]);
        }

        DB::beginTransaction();

        try {
            if ($validated['is_general_expense'] && !empty($validated['selected_apartments'])) {
                // Dépense générale : créer une dépense pour chaque appartement sélectionné
                $montantParAppartement = $validated['montant'] / count($validated['selected_apartments']);
                
                foreach ($validated['selected_apartments'] as $appartementId) {
                    Depense::create([
                        'date' => $validated['date'],
                        'type_depense' => $validated['type_depense'],
                        'description' => $validated['description'] . ' (Dépense générale)',
                        'appartement_id' => $appartementId,
                        'montant' => round($montantParAppartement, 2),
                    ]);
                }

                $message = 'Dépenses générales créées avec succès pour ' . count($validated['selected_apartments']) . ' appartement(s).';
            } else {
                // Dépense spécifique à un appartement
                Depense::create([
                    'date' => $validated['date'],
                    'type_depense' => $validated['type_depense'],
                    'description' => $validated['description'],
                    'appartement_id' => $validated['appartement_id'],
                    'montant' => $validated['montant'],
                ]);

                $message = 'Dépense créée avec succès.';
            }

            DB::commit();

            return redirect()->route('depenses.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création des dépenses : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' et la trace est  ' . $e->getTrace());
            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de l\'enregistrement des dépenses. Veuillez réessayer.'
            ]);
        }
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