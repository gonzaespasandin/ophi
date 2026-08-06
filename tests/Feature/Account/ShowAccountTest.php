<?php

namespace Tests\Feature\Account;

use App\Models\EmailChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_devuelve_el_estado_de_la_cuenta(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);

        $response = $this->actingAs($user)->getJson('/api/account');

        $response->assertOk()->assertJson([
            'email' => 'yo@ophi.test',
            'pending_email' => null,
            'newsletter_subscribed' => false,
        ]);
    }

    public function test_informa_el_email_pendiente_de_confirmacion(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);
        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
            'token' => hash('sha256', 'algun-token'),
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/account');

        $response->assertOk()->assertJson(['pending_email' => 'nuevo@ophi.test']);
    }

    public function test_una_solicitud_vencida_no_cuenta_como_pendiente(): void
    {
        $user = User::factory()->create();
        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
            'token' => hash('sha256', 'algun-token'),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/account');

        $response->assertOk()->assertJson(['pending_email' => null]);
    }
}
