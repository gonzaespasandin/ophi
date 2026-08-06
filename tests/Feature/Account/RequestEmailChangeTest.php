<?php

namespace Tests\Feature\Account;

use App\Models\EmailChangeRequest;
use App\Models\User;
use App\Notifications\EmailChangeRequestedNotification;
use App\Notifications\EmailChangeVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RequestEmailChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_crea_la_solicitud_sin_cambiar_el_email_actual(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'nuevo@ophi.test',
            'current_password' => 'password',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('email_change_requests', [
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
        ]);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }

    public function test_envia_el_link_al_nuevo_y_el_aviso_al_anterior(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);

        $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'nuevo@ophi.test',
            'current_password' => 'password',
        ]);

        $storedToken = EmailChangeRequest::where('user_id', $user->id)->value('token');

        Notification::assertSentOnDemand(
            EmailChangeVerificationNotification::class,
            function (EmailChangeVerificationNotification $notification, array $channels, $notifiable) use ($storedToken) {
                $mail = $notification->toMail($notifiable);

                $this->assertSame(hash('sha256', $notification->token), $storedToken);
                $this->assertStringContainsString('/confirmar-email/'.$notification->token, $mail->actionUrl);

                return true;
            }
        );

        Notification::assertSentTo($user, EmailChangeRequestedNotification::class);
    }

    public function test_rechaza_el_mismo_email_actual(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'actual@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'actual@ophi.test',
            'current_password' => 'password',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('email_change_requests', 0);
    }

    public function test_rechaza_una_contrasena_incorrecta(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'nuevo@ophi.test',
            'current_password' => 'incorrecta',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('email_change_requests', 0);
    }

    public function test_rechaza_un_email_ya_registrado(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        User::factory()->create(['email' => 'ocupado@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'ocupado@ophi.test',
            'current_password' => 'password',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('email_change_requests', 0);
    }

    public function test_una_solicitud_nueva_invalida_la_anterior(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'primero@ophi.test',
            'current_password' => 'password',
        ]);
        $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'segundo@ophi.test',
            'current_password' => 'password',
        ]);

        $this->assertDatabaseCount('email_change_requests', 1);
        $this->assertDatabaseHas('email_change_requests', ['new_email' => 'segundo@ophi.test']);
    }

    public function test_no_deja_la_solicitud_pendiente_si_falla_el_envio_al_nuevo_email(): void
    {
        config(['app.spa_url' => null]);
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/email', [
            'new_email' => 'nuevo@ophi.test',
            'current_password' => 'password',
        ]);

        $response->assertStatus(500);
        $this->assertDatabaseCount('email_change_requests', 0);
        $this->assertSame('viejo@ophi.test', $user->fresh()->email);
    }
}
