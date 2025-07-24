<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {$request->validate([
    'name' => 'required|string|max:255',
    'username' => [
        'required',
        'string',
        'max:255',
        'regex:/^\S*$/', // ❌ Tolak jika ada spasi
        'unique:users',
    ],
    'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
    'password' => [
        'required',
        'confirmed',
        Password::min(8)
            ->mixedCase()
            ->numbers()
            ->symbols(),
    ],
], [
    'username.regex' => 'Username tidak boleh mengandung spasi.',
]);


        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
