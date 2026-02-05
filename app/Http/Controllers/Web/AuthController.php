<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $status = AuthService::login($request);

        return to_route('admin.index');
    }

    public function logout(Request $request)
    {
        AuthService::logout($request);

        return to_route('login.show');
    }
}
