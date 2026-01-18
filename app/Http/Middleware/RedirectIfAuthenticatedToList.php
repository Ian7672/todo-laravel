<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedToList
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return redirect()->route('tasks.index');
        }

        // Lanjutkan permintaan dan tambahkan header untuk mencegah cache
        $response = $next($request);

        return $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                        ->header('Pragma', 'no-cache')
                        ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}

