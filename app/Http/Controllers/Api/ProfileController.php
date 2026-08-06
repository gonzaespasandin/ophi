<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profileService)
    {
    }

    public function get_auth_user_profiles(): JsonResponse
    {
        return response()->json($this->profileService->getAuthUserProfiles());
    }

    public function store(Request $request): JsonResponse
    {
        $user = User::with(['profiles', 'subscription'])->find(Auth::id());
        $userProfiles = $user->profiles;

        if (! $user->isPremium() && count($userProfiles) >= 1) {
            return response()->json(['message' => 'Usuario no premium'], 403);
        }

        if ($user->isPremium() && count($userProfiles) >= 10) {
            return response()->json(['message' => 'Máximo de 10 perfiles por usuario'], 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'ingredients' => 'nullable|array',
        ], [
            'name.required' => 'El nombre es obligatorio',
        ]);

        try {
            $profile = $this->profileService->store($data);

            return response()->json([
                'message' => 'Perfil creado correctamente',
                'profile' => $profile,
            ]);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 422);
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'avatar_color' => 'sometimes|nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'ingredients' => 'sometimes|nullable|array',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'avatar_color.regex' => 'El color no tiene un formato válido',
        ]);

        $profile = $this->profileService->update($id, $data);

        return response()->json([
            'message' => 'Perfil guardado',
            'profile' => $profile,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->profileService->destroy($id);

        return response()->json(['message' => 'Perfil eliminado']);
    }
}
