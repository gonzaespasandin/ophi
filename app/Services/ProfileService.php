<?php

namespace App\Services;

use App\Models\History;
use App\Models\HistoryResult;
use App\Models\Profile;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Error;
use Exception;
use Illuminate\Support\Arr;

class ProfileService 
{
    static public function getAuthUserProfiles() {
        if(!auth()->check()) {
            Log::debug('Usuario no está autenticado');
            return [];
        }

        Log::debug('Usuario está autenticado');
        $profiles = Profile::with(['ingredients' => function ($query) {
            $query->select('ingredients.id', 'ingredients.name', 'ingredients.icon', 'ingredients.is_group', 'ingredients.aliases');
        }])
            ->where('user_id', auth()->id())
            ->get(['id', 'name', 'avatar', 'user_id', 'is_main', 'created_at', 'updated_at']);

        return $profiles->map(function (Profile $profile) {
            $ingredientIds = $profile->ingredients
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->toArray();

            return [
                'id' => $profile->id,
                'name' => $profile->name,
                'avatar' => $profile->avatar,
                'user_id' => $profile->user_id,
                'is_main' => (bool) $profile->is_main,
                'created_at' => $profile->created_at,
                'updated_at' => $profile->updated_at,
                'ingredients' => $profile->ingredients->values(),
                'ingredient_ids' => $ingredientIds,
            ];
        })->values();
    }

    static public function store(array $data) {
        $repeatedName = Profile::where('user_id', Auth::id())->where('name', $data['name'])->exists();
        if($repeatedName) {
            throw new Exception('Ya tenés un perfil con ese nombre');
        }
        // ------------------------------------------------

        $profile = DB::transaction(function () use ($data) {  
            $profile = new Profile();
            $profile->name = $data['name'];
            $profile->avatar = $data['avatar'] ?? null;
            $profile->user_id = auth()->user()->id;
            $profile->save();

            $profile->ingredients()->attach($data['ingredients'] ?? []);

            return $profile;
        });

        return $profile;
    }

    static public function update(int $id, array $ingredients) {
        $profile = Profile::with('ingredients')->findOrFail($id);

        DB::transaction(function () use ($profile, $ingredients) {  
            $profile->ingredients()->sync($ingredients ?? []);
            $profile->save();
            return;
        });

        return $profile;
    }

    static public function destroy(int $id) {
        $profile = Profile::findOrFail($id);

        DB::transaction(function () use ($profile)  {  
            $profile->ingredients()->detach();
            $profile->delete();
        });
    }
}
