<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Check the user's role and redirect accordingly
                if (auth()->user()->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                } elseif (auth()->user()->isDealer()) {
                    return redirect()->route('dealer.dashboard');
                } elseif (auth()->user()->isAccounts()) {
                    return redirect()->route('accounts.dashboard');
                } elseif (auth()->user()->isTrader()) {
                    return redirect()->route('trader.dashboard');
                } elseif (auth()->user()->isOps()) {
                    return redirect()->route('ops.dashboard');
                } elseif (auth()->user()->isBackoffice()) {
                    return redirect()->route('backoffice.dashboard');
                }

                // Default redirection for users without a specific role
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
