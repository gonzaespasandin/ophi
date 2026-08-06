<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Error;

class AuthService
{
   static public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw new \Exception('Las credenciales no coinciden');
        }

        $user = auth()->user();

        if (DB::getSchemaBuilder()->hasTable('subscriptions')) {
            Subscription::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => 1,
                    'subscription_start_timestamp' => now(),
                    'subscription_end_timestamp' => null,
                    'auto_renovate' => false,
                ]
            );
        }

        if (DB::getSchemaBuilder()->hasTable('roles') && DB::getSchemaBuilder()->hasTable('role_user')) {
            $roleName = $user->role ?: 'user';
            $roleId = DB::table('roles')->where('name', $roleName)->value('id')
                ?: DB::table('roles')->where('name', 'user')->value('id');

            if ($roleId) {
                DB::table('role_user')->updateOrInsert([
                    'user_id' => $user->id,
                    'role_id' => $roleId,
                ]);
            }
        }

        $relations = [];
        if (DB::getSchemaBuilder()->hasTable('subscriptions')) {
            $relations[] = 'subscription.plan';
        }
        if (DB::getSchemaBuilder()->hasTable('roles') && DB::getSchemaBuilder()->hasTable('role_user')) {
            $relations[] = 'roles';
        }

        return $relations === [] ? $user : $user->load($relations);
    }

    static public function register(Array $data, String $name) 
    {        
        Log::debug('Todo bien en la validación :d');

        $user = DB::transaction(function () use ($data, $name) {
            $user = new User();
            $user->name = $name;
            $user->email = trim($data['email']);
            $user->password = Hash::make($data['password']);
            $user->save();
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => 1,
                'subscription_start_timestamp' => now(),
                'subscription_end_timestamp' => null,
                'auto_renovate' => false,
            ]);

            if (DB::getSchemaBuilder()->hasTable('roles')) {
                $roleId = DB::table('roles')->where('name', 'user')->value('id');

                if ($roleId) {
                    DB::table('role_user')->updateOrInsert([
                        'user_id' => $user->id,
                        'role_id' => $roleId,
                    ]);
                }
            }

            Profile::create([
                'name' => $name,
                'owner_id' => $user->id,
                'user_id' => $user->id,
                'is_main' => true,
            ]);

            return $user;
        });

        Log::info('Usuario registrado', ['user' => $user]);

        return $user;
    }

    static public function logout(Request $request): void
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
