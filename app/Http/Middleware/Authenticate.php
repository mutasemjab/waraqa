<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            // Check the route to determine which login page to redirect to
            if ($request->is('admin') || $request->is('admin/*')) {
                // Admin routes - redirect to admin login
                return route('admin.showlogin');
            } elseif ($request->is('provider') || $request->is('provider/*')) {
                // Provider routes - redirect to provider login
                return route('provider.login');
            } else {
                // User routes or general routes - redirect to user login
                return route('user.login');
            }
        }
    }
}