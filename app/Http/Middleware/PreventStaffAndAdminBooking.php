<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventStaffAndAdminBooking
{
    /**
     * Handle an incoming request.
     *
     * Prevents admin, manager, and staff from booking courts or accessing player booking areas,
     * routing them instead to their respective dashboards.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if ($user->isAdmin() || $user->hasRole('manager')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('staff')) {
                return redirect()->route('staff.today');
            }
        }

        return $next($request);
    }
}
