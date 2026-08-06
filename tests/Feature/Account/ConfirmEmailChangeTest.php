<?php

namespace Tests\Feature\Account;

use App\Models\EmailChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmEmailChangeTest extends TestCase
{
    use RefreshDatabase;

    private function crearSolicitud(User $user, string $newEmail, string $plainToken, ?\DateTimeInterface $expiresAt = null): void
    {
        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => $newEmail,
            'token' => hash('sha256', $plainToken),
            'expires_at' => $expiresAt ?? now()->addHour(),
        ]);
    }

    public function test_confirma_y_cambia_el_email(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->crearSolicitud($user, 'nuevo@ophi.test', 'token-valido');

        $response = $this->postJson('/api/account/email/confirm/token-valido');

        $response->assertOk();
        $this->assertSame('nuevo@ophi.test', $user->fresh()->email);
        $this->assertDatabaseCount('email_change_requests', 0);
    }

    public function test_rechaza_un_token_vencido(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->crearSolicitud($user, 'nuevo@ophi.test', 'token-vencido', now()->subMinute());

        $response = $this->postJson('/api/account/email/confirm/token-vencido');

        $response->assertStatus(410);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }

    public function test_rechaza_un_token_inexistente(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);

        $response = $this->postJson('/api/account/email/confirm/token-inventado');

        $response->assertStatus(410);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }

    public function test_rechaza_el_cambio_si_el_email_fue_tomado_mientras_tanto(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->crearSolicitud($user, 'ocupado@ophi.test', 'token-valido');
        User::factory()->create(['email' => 'ocupado@ophi.test']);

        $response = $this->postJson('/api/account/email/confirm/token-valido');

        $response->assertStatus(410);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }
}
