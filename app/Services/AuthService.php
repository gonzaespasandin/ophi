<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthService
{
   static public function login(Request $request): int
   {
        Log::info('LOGIN -----------------------------');
        Log::info('Email: '. $request->input('email'));
        Log::info('Contraseña: '. $request->input('password'));

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ],
        [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe incluir una @',
            'password.required' => 'La contraseña es obligatoria',
        ]);

        Log::info('Credenciales', [$credentials]);
        $foo = Auth::attempt($credentials);

        $user = User::where('email', $request->input('email'))->first();
        Log::info('USUARIO: ', [$user]);

        Log::info('Foo: ', [$foo]);

        if ($foo) {
            Log::info('Autenticado rey');
            $request->session()->regenerate();
            $status = 200;
        } else {
            Log::info('TOD MALLLLLLLLL');
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
