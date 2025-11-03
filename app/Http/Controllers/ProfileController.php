<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function get_auth_user_profiles(): \Illuminate\Http\JsonResponse
    {
        Log::debug('Obteniendo los resultados del perfil autenticado');

        if (auth()->check()) {
            Log::debug('Usuario está autenticado');
            $profiles = Profile::with('ingredients')->where('user_id', auth()->user()->id)->get();

            Log::info('Perfiles encontrados:', ['profiles' => $profiles]);

            return response()->json($profiles);
        } else {
            Log::debug('Usuario no está autenticado');
            return response()->json([], 401);
        }
    }
}
