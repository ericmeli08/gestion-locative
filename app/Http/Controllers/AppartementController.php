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
        $isCurrentlyActive = $appartement->active;

        if ($isCurrentlyActive) {
            // === DÉSACTIVATION ===

            // 1. Mémoriser et désactiver les charges récurrentes (charges "maîtresses" uniquement)
            $appartement->chargesMensuelles()
                ->whereNull('parent_charge_id')   // charges maîtresses
                ->where('recurrent', true)
                ->update([
                    'was_recurrent' => true,
                    'recurrent'     => false,
                ]);

            // 2. Désactiver l'appartement
            $appartement->update(['active' => false]);

        } else {
            // === RÉACTIVATION ===

            // 1. Restaurer la récurrence des charges qui l'étaient avant
            $appartement->chargesMensuelles()
                ->whereNull('parent_charge_id')
                ->where('was_recurrent', true)
                ->update([
                    'recurrent'     => true,
                    'was_recurrent' => false,
                ]);

            // 2. Mettre à jour la date de référence des charges réactivées
            //    pour que le service de génération parte du mois actuel
            //    et non du mois où l'appart a été désactivé.
            //    On met à jour la dernière charge enfant (ou la maîtresse si aucun enfant)
            //    pour qu'elle pointe sur le mois précédant aujourd'hui.
            $reactivatedCharges = $appartement->chargesMensuelles()
                ->whereNull('parent_charge_id')
                ->where('recurrent', true)
                ->get();

            $previousMonth = \Carbon\Carbon::now()->subMonth()->startOfMonth();

            foreach ($reactivatedCharges as $charge) {
                $lastChild = $charge->recurrentCharges()->latest('date_paiement')->first();

                if ($lastChild) {
                    // On avance la date de la dernière récurrence au mois précédent
                    // pour que la prochaine génération crée le mois courant
                    if (\Carbon\Carbon::parse($lastChild->date_paiement)->lt($previousMonth)) {
                        $lastChild->update(['date_paiement' => $previousMonth]);
                    }
                } else {
                    // Pas d'enfants : on met à jour la charge maîtresse elle-même
                    if (\Carbon\Carbon::parse($charge->date_paiement)->lt($previousMonth)) {
                        $charge->update(['date_paiement' => $previousMonth]);
                    }
                }
            }

            // 3. Réactiver l'appartement
            $appartement->update(['active' => true]);
        }

        return redirect()->back()
            ->with('success', __('messages.appartement.status_updated'));
    }
}
