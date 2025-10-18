<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login() 
    {
        return view('auth.login');
    }

    public function process(Request $request) 
    {   
        $request->validate(
            [
                'email' => 'required|min:3|max:255',
                'password' => 'required|min:5|max:255'
            ],
            [
                'email.required' => 'El email es obligatorio',
                'email.min' => 'El email debe tener mínimo 3 caracteres',
                'email.max' => 'El email no debe tener más 255 caracteres',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener más de 6 caracteres', 
                'password.max' => 'La contraseña np debe tener más de 255 caracteres'
            ] 
        );

        $credentials = $request->only(['email', 'password']);

        if(Auth::attempt($credentials)) {
            return to_route('home');
        }

        return to_route('auth.login.show');
    }
    

    public function logoutProcess() {
        Auth::logout();

        return to_route('auth.login.show');
    }
}
