<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
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

    public function test_envia_el_enlace_de_reset_a_una_cuenta_existente(): void
    {
        Notification::fake();
        $user = $this->createUser();

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertOk();
        $response->assertExactJson(['status' => __(Password::RESET_LINK_SENT)]);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /**
     * El front mostraba la tarjeta de "te enviamos el enlace" incluso cuando el
     * broker respondía throttled, porque todo salía con 200. El rechazo tiene
     * que llegar como error HTTP, con la clave cruda de Laravel como código
     * estable: el texto traducido depende del locale y de lang/*\/passwords.php.
     */
    public function test_rechaza_con_429_y_una_clave_estable_cuando_esta_throttleado(): void
    {
        Notification::fake();
        $user = $this->createUser();

        $this->postJson('/api/forgot-password', ['email' => $user->email])->assertOk();

        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertStatus(429);
        $response->assertJsonPath('code', Password::RESET_THROTTLED);
        $response->assertJsonPath('status', __(Password::RESET_THROTTLED));
    }

    /**
     * Una dirección sin cuenta se responde igual que un envío exitoso: si la
     * respuesta difiriera, cualquiera podría preguntarle a este endpoint
     * público y sin throttle qué emails están registrados.
     */
    public function test_responde_igual_ante_un_email_sin_cuenta_y_no_manda_nada(): void
    {
        Notification::fake();

        $response = $this->postJson('/api/forgot-password', [
            'email' => 'nadie@ophi.test',
        ]);

        $response->assertOk();
        $response->assertExactJson(['status' => __(Password::RESET_LINK_SENT)]);
        Notification::assertNothingSent();
    }

    public function test_no_permite_distinguir_una_cuenta_existente_de_una_inexistente(): void
    {
        Notification::fake();
        $user = $this->createUser();

        $conCuenta = $this->postJson('/api/forgot-password', ['email' => $user->email]);
        $sinCuenta = $this->postJson('/api/forgot-password', ['email' => 'nadie@ophi.test']);

        $this->assertSame($conCuenta->status(), $sinCuenta->status());
        $this->assertSame($conCuenta->json(), $sinCuenta->json());
    }

    public function test_rechaza_una_solicitud_sin_email(): void
    {
        $response = $this->postJson('/api/forgot-password', []);

        $response->assertStatus(422);
    }

    public function test_rechaza_un_email_con_formato_invalido(): void
    {
        $response = $this->postJson('/api/forgot-password', [
            'email' => 'esto-no-es-un-email',
        ]);

        $response->assertStatus(422);
    }
}
