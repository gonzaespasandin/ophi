<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        //------------ Chequeo de subscription del usuario autenticado
        $user = User::with(['profiles', 'subscription'])
        ->find(Auth::id());
        $userProfiles = $user->profiles;

        if(!$user->isPremium() && count($userProfiles) >= 1) {
            return view('subscription.index', [
                'message' => 'Desbloqueá el premium para obtener más perfiles'
            ]);
        }
        if($user->isPremium() && count($userProfiles) > 10) {
            return view('subscription.index', [
                'message' => 'No más de 10 perfiles por usuario!'
            ]);
        }
        //-------------

        Log::debug('Guardando un perfil de un usuario autenticado');
        $data = $request->validate([
            'name' => 'required',
        ],
        [
            'name.required' => 'El nombre es obligatorio',
        ]);
        
        Log::debug('La validación es correcta');
        Log::info('Datos del perfil:', ['data' => $data]);

        $profile = new Profile();
        $profile->name = $data['name'];
        $profile->avatar = $data['avatar'] ?? null;
        $profile->user_id = auth()->user()->id;
        $profile->save();

        $profile->ingredients()->attach($data['ingredients'] ?? []);

       return response()->json([
            'feedback' => 'Perfil creado correctamente',
            'profile' => $profile
        ]);
    }

    public function update(int $id, Request $request) {
        Log::debug('Actualizando el perfil de un usuario autenticado');
        Log::info('Ingredientes', ['key' => $request->input('ingredients', [])]);
        Log::info('[]', ['key' => $request['ingredients[]']]);

        $profile = Profile::with('ingredients')->findOrFail($id);

        $profile->ingredients()->sync($request['ingredients'] ?? []);
        $profile->save();

        return response()->json([
            'feedback' => 'Perfil guardado',
            'profile' => $profile
        ]);
    }

    public function destroy(int $id) {
        $profile = Profile::findOrFail($id);

        $profile->ingredients()->detach();
        $profile->delete();

        return response()->json([
            'feedback' => 'Perfil eliminado'
        ]);
    }
}
