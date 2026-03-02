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
        $status = AuthService::login($request);

        if ($status === 401) {
            Session::flash('feedback.message', 'Credenciales inválidas');
            Session::flash('feedback.type', 'danger');

            return to_route('login.show');
        }

        return to_route('admin.index');
    }

    public function logout(Request $request)
    {
        AuthService::logout($request);

        return to_route('login.show');
    }
}
