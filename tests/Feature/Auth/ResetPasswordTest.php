<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'name' => 'Lucía',
            'email' => 'lucia@ophi.test',
            'password' => Hash::make('ViejaPass1'),
        ]);
    }

    public function test_reinicia_la_contrasena_con_un_token_valido(): void
    {
        $user = $this->createUser();
        $token = Password::createToken($user);

        $response = $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NuevaPass1',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('NuevaPass1', $user->fresh()->password));
    }

    /**
     * El front no puede distinguir el token vencido leyendo el texto traducido
     * del status: depende del locale y de lang/es/passwords.php. Necesita un
     * código HTTP de error y una clave estable con la cual ramificar.
     */
    public function test_rechaza_un_token_invalido_con_422_y_una_clave_estable(): void
    {
        $user = $this->createUser();

        $response = $this->postJson('/api/reset-password', [
            'token' => 'token-que-no-existe',
            'email' => $user->email,
            'password' => 'NuevaPass1',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('code', Password::INVALID_TOKEN);
        $this->assertTrue(Hash::check('ViejaPass1', $user->fresh()->password));
    }

    public function test_rechaza_un_email_sin_cuenta_sin_delatar_que_no_existe(): void
    {
        $response = $this->postJson('/api/reset-password', [
            'token' => 'cualquier-token',
            'email' => 'nadie@ophi.test',
            'password' => 'NuevaPass1',
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('code', Password::INVALID_TOKEN);
    }

    /**
     * Un email sin cuenta y un token muerto tienen que responder exactamente lo
     * mismo. Si difieren, cualquiera puede preguntarle al endpoint qué
     * direcciones estan registradas, que es justo lo que forgot_password evita
     * respondiendo siempre 200.
     */
    public function test_no_permite_distinguir_una_cuenta_existente_de_una_inexistente(): void
    {
        $user = $this->createUser();

        $conCuenta = $this->postJson('/api/reset-password', [
            'token' => 'token-que-no-existe',
            'email' => $user->email,
            'password' => 'NuevaPass1',
        ]);

        $sinCuenta = $this->postJson('/api/reset-password', [
            'token' => 'token-que-no-existe',
            'email' => 'nadie@ophi.test',
            'password' => 'NuevaPass1',
        ]);

        $this->assertSame($conCuenta->status(), $sinCuenta->status());
        $this->assertSame($conCuenta->json(), $sinCuenta->json());
    }
}
