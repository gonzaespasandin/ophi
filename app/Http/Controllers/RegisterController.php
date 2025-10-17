<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register() {
        return view('auth.register');
    }

    public function process(Request $request)  
    {
        $request->validate(
            [
                'email' => 'required|min:3|max:255',
                'name' => 'required|min:3|max:55',
                'password' => 'required|min:6|max:255'
            ],
            [
                'email.required' => 'El email es obligatorio',
                'email.min' => 'El email debe tener mínimo 3 caracteres',
                'email.max' => 'El email no debe tener más 255 caracteres',
                'name.required' => 'El nombre es obligatorio',
                'name.min' => 'El nombre debe tener mínimo 3 caracteres',
                'name.max' => 'El nombre no debe tener más de 55 caracteres',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener más de 6 caracteres', 
                'password.max' => 'La contraseña np debe tener más de 255 caracteres'
            ] 
        );

    

        $data = $request->only(['email', 'name', 'password']);

        $user = User::create($data);

        // Logueo al usuario para luego usar su ID para su perfil médico
        Auth::login($user);
    
        return to_route('profile.create.show');
    }
}
