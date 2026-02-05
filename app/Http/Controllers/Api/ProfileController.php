<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
            $profiles = Profile::with('ingredients.ingredients.ingredients.ingredients')->where('user_id', auth()->user()->id)->get();
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

    public function update(int $id, Request $request) {
        Log::debug('Actualizando el perfil de un usuario autenticado');
        Log::info('Ingredientes', ['key' => $request->input('ingredients', [])]);
        Log::info('[]', ['key' => $request['ingredients[]']]);

        $profile = Profile::with('ingredients')->findOrFail($id);

        $profile->ingredients()->sync($request['ingredients'] ?? []);
        $profile->save();

        return response()->json($profile);
    }

    public function destroy(int $id) {
        $profile = Profile::findOrFail($id);

        $profile->ingredients()->detach();
        $profile->delete();
    }
}
