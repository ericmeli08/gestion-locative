<?php

namespace App\Http\Controllers;

use App\Models\ChargeMensuelle;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            if($request->recurrent === 'true')
                $query->where('recurrent', true);
            elseif($request->recurrent === 'false')
                $query->where('recurrent', false);
            else{
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
        ]);

        ChargeMensuelle::create($validated);

        return redirect()->route('charges.index')
            ->with('success', 'Charge créée avec succès.');
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

        return redirect()->route('charges.index')
            ->with('success', 'Charge mise à jour avec succès.');
    }

    public function destroy(ChargeMensuelle $charge)
    {
        $charge->delete();

        return redirect()->route('charges.index')
            ->with('success', 'Charge supprimée avec succès.');
    }
}
