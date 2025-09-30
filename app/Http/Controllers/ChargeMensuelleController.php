<?php

namespace App\Http\Controllers;

use App\Models\ChargeMensuelle;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ChargeMensuelleController extends Controller
{
    public function index(Request $request)
    {
        $query = ChargeMensuelle::with('appartement');

        if ($request->filled('search')) {
            $query->where('service', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('apartment')) {
            $query->where('appartement_id', $request->apartment);
        }

        if ($request->filled('recurrent')) {
            if ($request->recurrent === 'true') {
                $query->where('recurrent', true);
            } elseif ($request->recurrent === 'false') {
                $query->where('recurrent', false);
            } else {
                $query->where('recurrent', true);
                $query->whereNull('parent_charge_id');
            }
        }

        $charges = $query->latest('date_paiement')->paginate(15)->withQueryString();
        $apartments = Appartement::all();

        return Inertia::render('ChargesMensuelles/Index', [
            'charges' => $charges,
            'apartments' => $apartments,
            'filters' => $request->only(['search', 'apartment', 'recurrent']),
        ]);
    }

    public function create()
    {
        $apartments = Appartement::all();

        return Inertia::render('ChargesMensuelles/Form', [
            'apartments' => $apartments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service' => 'required|string|max:255',
            'appartement_id' => 'nullable|exists:appartements,id',
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'recurrent' => 'boolean',
            'notes' => 'nullable|string',
            'is_general_charge' => 'boolean',
            'selected_apartments' => 'array',
            'selected_apartments.*' => 'exists:appartements,id',
        ]);

        ChargeMensuelle::create($validated);
        // Validation personnalisée pour les charges générales
        if ($validated['is_general_charge'] && empty($validated['selected_apartments'])) {
            return back()->withErrors([
                'selected_apartments' => 'Veuillez sélectionner au moins un appartement pour une charge générale.',
            ]);
        }

        return redirect()->route('charges.index')->with('success', 'Charge créée avec succès.');
        DB::beginTransaction();

        try {
            if ($validated['is_general_charge'] && !empty($validated['selected_apartments'])) {
                // Charge générale : créer une charge pour chaque appartement sélectionné
                $montantParAppartement = $validated['montant'] / count($validated['selected_apartments']);

                foreach ($validated['selected_apartments'] as $appartementId) {
                    ChargeMensuelle::create([
                        'service' => $validated['service'] . ' (Charge générale)',
                        'appartement_id' => $appartementId,
                        'date_paiement' => $validated['date_paiement'],
                        'montant' => round($montantParAppartement, 2),
                        'recurrent' => $validated['recurrent'],
                        'notes' => $validated['notes'],
                    ]);
                }

                $message = 'Charges générales créées avec succès pour ' . count($validated['selected_apartments']) . ' appartement(s).';
            } else {
                // Charge spécifique à un appartement
                ChargeMensuelle::create([
                    'service' => $validated['service'],
                    'appartement_id' => $validated['appartement_id'],
                    'date_paiement' => $validated['date_paiement'],
                    'montant' => $validated['montant'],
                    'recurrent' => $validated['recurrent'],
                    'notes' => $validated['notes'],
                ]);

                $message = 'Charge créée avec succès.';
            }

            DB::commit();

            return redirect()->route('charges.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création des dépenses : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' et la trace est  ' . $e->getTrace());
            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de l\'enregistrement des dépenses. Veuillez réessayer.',
            ]);
        }
    }

    public function edit(ChargeMensuelle $charge)
    {
        $apartments = Appartement::all();

        return Inertia::render('ChargesMensuelles/Form', [
            'charge' => $charge,
            'apartments' => $apartments,
        ]);
    }

    public function update(Request $request, ChargeMensuelle $charge)
    {
        $validated = $request->validate([
            'service' => 'required|string|max:255',
            'appartement_id' => 'nullable|exists:appartements,id',
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'recurrent' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $charge->update($validated);

        return redirect()->route('charges.index')->with('success', 'Charge mise à jour avec succès.');
    }

    public function destroy(ChargeMensuelle $charge)
    {
        $charge->delete();

        return redirect()->route('charges.index')->with('success', 'Charge supprimée avec succès.');
    }
}
