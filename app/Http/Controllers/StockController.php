<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $query = Stock::with('appartement');

        if ($request->filled('search')) {
            $query->where('element', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('apartment')) {
            $query->where('appartement_id', $request->apartment);
        }

        if ($request->filled('alert')) {
            if ($request->alert === 'low') {
                $query->whereRaw('reste <= seuil');
            }
        }

        $stocks = $query->latest()->paginate(15)->withQueryString();
        $apartments = Appartement::all();

        return Inertia::render('Stocks/Index', [
            'stocks' => $stocks,
            'apartments' => $apartments,
            'filters' => $request->only(['search', 'apartment', 'alert']),
        ]);
    }

    public function create()
    {
        $apartments = Appartement::all();

        return Inertia::render('Stocks/Form', [
            'apartments' => $apartments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'element' => 'required|string|max:255',
            'quantite_mois' => 'required|integer|min:0',
            'utilise' => 'required|integer|min:0',
            'seuil' => 'required|integer|min:0',
            'appartement_id' => 'nullable|exists:appartements,id',
        ]);

        Stock::create($validated);

        return redirect()->route('stocks.index')
            ->with('success', 'Stock créé avec succès.');
    }

    public function edit(Stock $stock)
    {
        $apartments = Appartement::all();

        return Inertia::render('Stocks/Form', [
            'stock' => $stock,
            'apartments' => $apartments,
        ]);
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'element' => 'required|string|max:255',
            'quantite_mois' => 'required|integer|min:0',
            'utilise' => 'required|integer|min:0',
            'seuil' => 'required|integer|min:0',
            'appartement_id' => 'nullable|exists:appartements,id',
        ]);

        $stock->update($validated);

        return redirect()->route('stocks.index')
            ->with('success', 'Stock mis à jour avec succès.');
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')
            ->with('success', 'Stock supprimé avec succès.');
    }
}