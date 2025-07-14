<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on the authenticated user's guard/role
                
                // Check if user is admin
                if (Auth::guard('admin')->check()) {
                    return redirect()->route('admin.dashboard');
                }
                
                // Check if user is provider
                if (Auth::guard('provider')->check()) {
                    return redirect()->route('provider.dashboard');
                }
                
                // Check if user is regular user (web guard)
                if (Auth::guard('web')->check()) {
                    return redirect()->route('user.dashboard');
                }
                
                // Fallback: redirect based on request path
                if ($request->is('admin') || $request->is('admin/*')) {
                    return redirect()->route('admin.dashboard');
                } elseif ($request->is('provider') || $request->is('provider/*')) {
                    return redirect()->route('provider.dashboard');
                } else {
                    return redirect()->route('user.dashboard');
                }
            }
        }

        return $next($request);
    }
}