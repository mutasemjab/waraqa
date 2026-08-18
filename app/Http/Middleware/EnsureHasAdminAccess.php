<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHasAdminAccess
{
    /**
     * Allow into the admin panel any user who has been granted at least one
     * permission, regardless of which role that permission came through.
     * Fine-grained access to each section/action is then enforced by the
     * `permission:` middleware on individual routes/controllers.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || $user->getAllPermissions()->isEmpty()) {
            abort(403);
        }

        return $next($request);
    }
}
