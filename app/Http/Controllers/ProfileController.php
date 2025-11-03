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

    public function store(Request $request) {
        Log::debug('Guardando un perfil de un usuario autenticado');
        $data = $request->validate([
            'name' => 'required',
            'ingredients' => 'required',
        ]);
        Log::debug('La validación es correcta');
        Log::info('Datos del perfil:', ['data' => $data]);

        $profile = new Profile();
        $profile->name = $data['name'];
        $profile->avatar = $data['avatar'] ?? null;
        $profile->user_id = auth()->user()->id;
        $profile->save();

        $profile->ingredients()->attach($data['ingredients'] ?? []);

        return response()->json($profile);
    }
}
