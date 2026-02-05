<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{
   static public function login(Request $request): int
   {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ],
        [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe incluir una @',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $status = 200;
        } else {
            $status = 401;
        }

        return $status;
    }

    static public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
