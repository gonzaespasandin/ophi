<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            AuthService::login($credentials);
            $request->session()->regenerate();

            return to_route('admin.index');
        } catch (\Exception) {
            Session::flash('feedback.message', 'Credenciales invalidas');
            Session::flash('feedback.type', 'danger');

            return to_route('login.show');
        }
    }

    public function logout(Request $request)
    {
        AuthService::logout($request);

        return to_route('login.show');
    }
}
