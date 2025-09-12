<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id'    => $request->user()->id,
                    'name'  => $request->user()->name,
                    'username' => $request->user()->username, // ✅ tambahin username
                    'email' => $request->user()->email,
                    'profile_photo_url' => $request->user()->profile_photo_url,

                    // tambahan field custom
                    'bio' => $request->user()->bio,
                    'background_photo_path' => $request->user()->background_photo_path,

                    // roles & permissions dari Spatie
                    'roles' => $request->user()->getRoleNames(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                ] : null,
            ],
        ];
    }

    /**
     * Handle an incoming request.
     */
    public function handle($request, \Closure $next)
    {
        $response = parent::handle($request, $next);

        if ($request->user() === null) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
