<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^\S*$/', // Tidak boleh mengandung spasi
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            'password' => [
                'nullable',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols(),
            ],
            'bio' => ['nullable', 'string', 'max:500'], // Tambahkan bio
        ];
    }

    /**
     * Custom messages untuk validasi.
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'Username tidak boleh mengandung spasi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixedCase' => 'Password harus mengandung huruf besar dan kecil.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.symbols' => 'Password harus mengandung simbol.',
        ];
    }

}
