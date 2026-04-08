<?php

namespace App\Http\Middleware;

use App\Services\RecurringChargeService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRecurringCharges
{

    protected $chargeService;

    public function __construct(RecurringChargeService $chargeService)
    {
        $this->chargeService = $chargeService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Exécute la vérification et la génération si nécessaire
        $this->chargeService->generateMissingCharges();

        return $next($request);
    }
}
