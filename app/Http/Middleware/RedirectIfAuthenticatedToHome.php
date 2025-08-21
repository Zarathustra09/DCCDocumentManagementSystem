<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedToHome
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
