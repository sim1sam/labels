<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has the required role
        if ($role === 'admin' && !$user->isAdmin()) {
            abort(403, 'Access denied. Admin privileges required.');
        }
        
        if ($role === 'merchant' && !$user->isMerchant()) {
            abort(403, 'Access denied. Merchant privileges required.');
        }

        return $next($request);
    }
}
