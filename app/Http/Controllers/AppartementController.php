<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppartementRequest;
use App\Models\Appartement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AppartementController extends Controller
{
    public function index(Request $request)
    {
        $query = Appartement::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('active', $request->status === 'active');
        }

        // Capacity filter
        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        $appartements = $query->orderBy('created_at', 'desc')
                             ->paginate(10)
                             ->withQueryString();

        return Inertia::render('Appartement/Index', [
            'appartements' => $appartements,
            'filters' => $request->only(['search', 'status', 'capacity'])
        ]);
    }

    public function create()
    {
        return Inertia::render('Appartement/Form');
    }

    public function store(AppartementRequest $request)
    {
        Appartement::create($request->validated());

        return redirect()->route('appartements.index')
                        ->with('success', __('messages.appartement.created'));
    }

    public function edit(Appartement $appartement)
    {
        return Inertia::render('Appartement/Form', [
            'appartement' => $appartement
        ]);
    }

    public function update(AppartementRequest $request, Appartement $appartement)
    {
        $appartement->update($request->validated());

        return redirect()->route('appartements.index')
                        ->with('success', __('messages.appartement.updated'));
    }

    public function destroy(Appartement $appartement)
    {
        $appartement->delete();

        return redirect()->route('appartements.index')
                        ->with('success', __('messages.appartement.deleted'));
    }

    public function toggleStatus(Appartement $appartement)
    {
        $appartement->update(['active' => !$appartement->active]);

        return redirect()->back()
                        ->with('success', __('messages.appartement.status_updated'));
    }
}
