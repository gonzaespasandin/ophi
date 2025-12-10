<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{

    public function login(Request $request)
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
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => auth()->user()
            ], 200);
        }

        return response()->json([
            'message' => 'Las credenciales no coinciden con nuestros registros'
        ], 401);
    }

    public function register(Request $request) {
        Log::debug('Registrando usuario...');
        $data = $request->validate([
            'terms_and_conditions' => 'required',
            'name' => 'required|min:3|max:25|regex:/^[a-zA-ZÁÉÍÓÚÜáéíóúüÑñ\s-]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:74|regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/',
            'confirm_password' => 'required|same:password',
        ],
        [
            'terms_and_conditions.required' => 'Debes aceptar los términos y condiciones',
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener mínimo 3 caracteres',
            'name.max' => 'El nombre debe tener máximo 25 caracteres',
            'name.regex' => 'El nombre debe contener únicamente letras',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener un mínimo de 8 caracteres',
            'password.max' => 'La contraseña debe tener máximo 74 caracteres',
            'password.regex' => 'La contraseña debe tener al menos 1 letra minúscula y otra mayúscula',
            'confirm_password.required' => 'La confirmación de contraseña es obligatoria',
            'confirm_password.same' => 'Las contraseñas no coinciden',
        ]);
        Log::debug('Todo bien en la validación :d');

        $user = new User();
        $user->name = trim($data['name']);
        $user->email = trim($data['email']);
        $user->password = Hash::make($data['password']);
        $user->save();
        Log::info('Usuario registrado', ['user' => $user]);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
        ], 201);
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
