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

        if ($user && (
            empty($user->username) ||              // Belum isi username
            str_starts_with($user->username, 'google_') // Dummy username dari login Google
        )) {
            if (!$request->is('complete-profile') && !$request->is('logout')) {
                return redirect()->route('complete-profile');
            }
        }

        return $next($request);
    }
}
