<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (is_null($user->username) || is_null($user->password))) {
            // Jangan redirect kalau user sudah di halaman complete-profile
            if (!$request->is('complete-profile')) {
                return redirect()->route('complete-profile');
            }
        }

        return $next($request);
    }
}
