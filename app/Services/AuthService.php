<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Error;

class AuthService
{
   static public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new \Exception('Las credenciales no coinciden');
        }

        return auth()->user();
    }

    static public function register(Array $data, String $name) 
    {        
        Log::debug('Todo bien en la validación :d');

        $user = DB::transaction(function () use ($data, $name) {
            $user = new User();
            $user->name = $name;
            $user->email = trim($data['email']);
            $user->password = Hash::make($data['password']);
            $user->save();
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => 1,
            ]);
            return $user;
        });

        Log::info('Usuario registrado', ['user' => $user]);

        return $user;
    }

    static public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
