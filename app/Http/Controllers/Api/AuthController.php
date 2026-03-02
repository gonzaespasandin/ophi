<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $status = AuthService::login($request);

        Log::info('Login ......................................................');
        Log::info('Status: ' . $status);

        if ($status === 401) {
            Log::info('Las credenciales no coinciden');
            return response()->json([
                'message' => 'Las credenciales no coinciden con nuestros registros'
            ], $status);
        }

        if ($status === 200) {
            Log::info('Puede iniciar sesión :)');
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => auth()->user()
            ], $status);
        }

        Log::info('Algo salió mal :(');
        return response()->json([
            'message' => 'Error no controlado',
            'status' => $status
        ]);
    }

    public function register(Request $request) {
        Log::debug('Registrando usuario...');
        $data = $request->validate([
            'terms_and_conditions' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:74|regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/',
            'confirm_password' => 'required|same:password',
        ],
        [
            'terms_and_conditions.required' => 'Debes aceptar los términos y condiciones',
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener un mínimo de 8 caracteres',
            'password.max' => 'La contraseña debe tener máximo 74 caracteres',
            'password.regex' => 'La contraseña debe tener al menos 1 letra minúscula y otra mayúscula',
            'confirm_password.required' => 'La confirmación de contraseña es obligatoria',
            'confirm_password.same' => 'Las contraseñas no coinciden',
        ]);
        Log::debug('Todo bien en la validación :d');

        $name = trim($request->input('name'));
        if (strlen($name) > 24) {
            $name = substr($name, 0, 24);
        }

        $user = DB::transaction(function () use ($data, $name) {
            $user = new User();
            $user->name = $name;
            $user->email = trim($data['email']);
            $user->password = Hash::make($data['password']);
            $user->save();
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $user;
        });
        Log::info('Usuario registrado', ['user' => $user]);

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        AuthService::logout($request);

        return response()->noContent();
    }

    public function forgot_password(Request $request) {
        Log::info('------------------------------------------------------------------------------------------');
        Log::info('[AuthController forgot_password()]');
        $request->validate(['email' => 'required|email']);

        Log::info('Email validated...');
        $status = Password::sendResetLink(
            $request->only('email')
        );

        Log::info('Status thing done...', ['status' => $status]);
        return response()->json(['status' => __($status)]);
    }

    public function reset_password(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|max:74|regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json(['status' => __($status)]);
    }
}
