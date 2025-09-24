<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
class ClientAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'client') {
            $allowedRoutes = [
                'clients.personal',
                'clients.invoice_client',
                'clients.appointments_client',
                'clients.SupplyOrders_client',
                'clients.questions_client',
                'clients.profile',
                'clients.Client_store'
            ];

            if (!in_array(Route::currentRouteName(), $allowedRoutes)) {
                return redirect()->route('clients.personal')->with('error', 'غير مصرح لك بالدخول إلى هذه الصفحة.');
            }
        }

        return $next($request);
    }
}