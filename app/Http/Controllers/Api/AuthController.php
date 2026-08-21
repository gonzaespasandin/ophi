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
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $user = AuthService::login($credentials);

            $request->session()->regenerate();

            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 401);
        }
    }


    public function register(Request $request) {
        $data = $request->validate([
            'terms_and_conditions' => 'required|accepted',
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'required|min:8|max:74|regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/',
            'confirm_password' => 'required|same:password',
            // El wizard manda acá los ingredientes que el usuario eligió evitar.
            // Sin estas reglas validate() los descartaba y el perfil principal
            // nacía vacío. La selección vacía es válida: se puede completar después.
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'integer|exists:ingredients,id',
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
            'ingredients.array' => 'Los ingredientes deben ser una lista',
            'ingredients.*.integer' => 'Alguno de los ingredientes seleccionados no es válido',
            'ingredients.*.exists' => 'Alguno de los ingredientes seleccionados no existe',
        ]);

        $name = trim($request->input('name'));
        if (strlen($name) > 24) {
            $name = substr($name, 0, 24);
        }

        $user = AuthService::register($data, $name);

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
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        // El status traducido no sirve para ramificar en el cliente: depende del
        // locale y de lang/*/passwords.php. Un pedido throttleado tiene que llegar
        // como error HTTP, con la clave cruda de Laravel como código estable; si no,
        // el front muestra la tarjeta de "te enviamos el enlace" sin que haya mail.
        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
                'status' => __(Password::RESET_THROTTLED),
                'code' => Password::RESET_THROTTLED,
            ], 429);
        }

        // Una dirección sin cuenta se responde igual que un envío exitoso:
        // devolver INVALID_USER distinguiría un email registrado de uno que no lo
        // está, y este endpoint es público y sin throttle. Quien pidió el reset
        // no gana nada con la diferencia; quien enumera cuentas, sí.
        if ($status === Password::RESET_LINK_SENT || $status === Password::INVALID_USER) {
            return response()->json(['status' => __(Password::RESET_LINK_SENT)]);
        }

        // Cualquier otra clave es un caso que el broker no debería devolver acá:
        // se registra para poder verlo y se responde como error del servidor, sin
        // afirmarle al usuario que el mail salió.
        Log::warning('Estado inesperado al enviar el enlace de reset', ['status' => $status]);

        return response()->json(['status' => __($status)], 500);
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

        // El status traducido no sirve para ramificar en el cliente: depende del
        // locale y de lang/*/passwords.php. Un enlace vencido tiene que llegar
        // como error HTTP, con la clave cruda de Laravel como código estable.
        //
        // Todos los fallos responden INVALID_TOKEN, incluido el email sin cuenta:
        // devolver INVALID_USER distinguiría una dirección registrada de una que
        // no lo está, y este endpoint es público y sin throttle. Para quien pide
        // el reset la causa es la misma —el enlace no sirve— y la acción también.
        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'status' => __(Password::INVALID_TOKEN),
                'code' => Password::INVALID_TOKEN,
            ], 422);
        }

        return response()->json(['status' => __($status)]);
    }
}
