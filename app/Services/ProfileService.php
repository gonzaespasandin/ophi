<?php

namespace App\Services;

use App\Exceptions\MainProfileDeletionException;
use App\Models\Profile;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileService
{
    public function getAuthUserProfiles(): Collection
    {
        if (! Auth::check()) {
            return collect();
        }

        $profiles = Profile::with(['ingredients' => function ($query) {
            $query->select('ingredients.id', 'ingredients.name', 'ingredients.icon', 'ingredients.is_group', 'ingredients.aliases');
        }])
            ->where(function ($query) {
                $query->where('owner_id', Auth::id())
                    ->orWhere('user_id', Auth::id());
            })
            ->get(['id', 'name', 'avatar', 'avatar_color', 'user_id', 'owner_id', 'is_main', 'created_at', 'updated_at']);

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
                'avatar_color' => $profile->avatar_color,
                'user_id' => $profile->owner_id ?? $profile->user_id,
                'owner_id' => $profile->owner_id ?? $profile->user_id,
                'is_main' => (bool) $profile->is_main,
                'created_at' => $profile->created_at,
                'updated_at' => $profile->updated_at,
                'ingredients' => $profile->ingredients->values(),
                'ingredient_ids' => $ingredientIds,
            ];
        })->values();
    }

    public function store(array $data): Profile
    {
        $repeatedName = Profile::where(function ($query) {
            $query->where('owner_id', Auth::id())
                ->orWhere('user_id', Auth::id());
        })->where('name', $data['name'])->exists();

        if ($repeatedName) {
            throw new Exception('Ya tenés un perfil con ese nombre');
        }

        return DB::transaction(function () use ($data) {
            $profile = new Profile();
            $profile->name = $data['name'];
            $profile->avatar = $data['avatar'] ?? null;
            $profile->avatar_color = $data['avatar_color'] ?? null;
            $profile->user_id = Auth::id();
            $profile->owner_id = Auth::id();
            $profile->save();

            $profile->ingredients()->attach($data['ingredients'] ?? []);

            return $profile;
        });
    }

    public function update(int $id, array $data): Profile
    {
        $profile = $this->findOwned($id);

        return DB::transaction(function () use ($profile, $data) {
            if (array_key_exists('name', $data)) {
                $profile->name = $data['name'];
            }

            if (array_key_exists('avatar_color', $data)) {
                $profile->avatar_color = $data['avatar_color'];
            }

            $profile->save();

            if (array_key_exists('ingredients', $data)) {
                $profile->ingredients()->sync($data['ingredients'] ?? []);
            }

            return $profile->load('ingredients');
        });
    }

    public function destroy(int $id): void
    {
        $profile = $this->findOwned($id);

        if ($profile->is_main) {
            throw new MainProfileDeletionException();
        }

        DB::transaction(function () use ($profile) {
            $profile->ingredients()->detach();
            $profile->delete();
        });
    }

    private function findOwned(int $id): Profile
    {
        return Profile::with('ingredients')
            ->where('owner_id', Auth::id())
            ->findOrFail($id);
    }
}
