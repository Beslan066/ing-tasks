<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SitePasswordProtect
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('site-password.form', 'site-password.check')
            || $request->is('up')) {
            return $next($request);
        }

        if (! session('site_unlocked')) {
            return redirect()->route('site-password.form');
        }

        return $next($request);
    }
}
