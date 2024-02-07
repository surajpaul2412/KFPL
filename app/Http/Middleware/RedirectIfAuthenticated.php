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
                $user = auth()->user();

                // Check if the user's status is not equal to 1
                if ($user->status != 1) {
                    Auth::logout(); // Log out the user
                    return redirect()->route('login')->with('error', 'Your account is not active.');
                }

                // Check the user's role and redirect accordingly
                if ($user->isAdmin()) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->isDealer()) {
                    return redirect()->route('dealer.dashboard');
                } elseif ($user->isAccounts()) {
                    return redirect()->route('accounts.dashboard');
                } elseif ($user->isTrader()) {
                    return redirect()->route('trader.dashboard');
                } elseif ($user->isOps()) {
                    return redirect()->route('ops.dashboard');
                } elseif ($user->isBackoffice()) {
                    return redirect()->route('backoffice.dashboard');
                }

                // Default redirection for users without a specific role
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
