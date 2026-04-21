<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function get_auth_user_profiles(): \Illuminate\Http\JsonResponse
    {
        Log::debug('Obteniendo los resultados del perfil autenticado');
        $profiles = ProfileService::getAuthUserProfiles();

        return response()->json($profiles);
    }

    public function store(Request $request) {

        //------------ Chequeo de subscription del usuario autenticado
        $user = User::with(['profiles', 'subscription'])
        ->find(Auth::id());
        $userProfiles = $user->profiles;
        if(!$user->isPremium() && count($userProfiles) >= 1) {
            return response()->json([
                'message' => 'Usuario no premium'
            ], 403);
        }
        if($user->isPremium() && count($userProfiles) >= 10) {
            return response()->json([
                'message' => 'Máximo de 10 perfiles por usuario'
            ], 403);
        }
        //-------------

        Log::debug('Guardando un perfil de un usuario autenticado');
        $data = $request->validate([
            'name' => 'required',
            'ingredients' => 'nullable|array',
        ],
        [
            'name.required' => 'El nombre es obligatorio',
        ]);
        
        Log::debug('La validación es correcta');
        Log::info('Datos del perfil:', ['data' => $data]);


       try {
        $profile = ProfileService::store($data);
        return response()->json([
            'message' => 'Perfil creado correctamente',
            'profile' => $profile
        ]);
       } catch (\Exception $e) {
        return response()->json([
            'errors' => $e
        ], 422);
       }
    }

    public function update(int $id, Request $request) {
        Log::debug('Actualizando el perfil de un usuario autenticado');
        Log::info('Ingredientes', ['key' => $request->input('ingredients', [])]);
        Log::info('[]', ['key' => $request['ingredients[]']]);

        $ingredients = $request->inout('ingredients');
        $profile = ProfileService::update($id, $ingredients);
        
        return response()->json([
            'message' => 'Perfil guardado',
            'profile' => $profile
        ]);
    }

    public function destroy(int $id) {
        ProfileService::destroy($id);
        
        return response()->json([
            'message' => 'Perfil eliminado'
        ]);
    }
}
