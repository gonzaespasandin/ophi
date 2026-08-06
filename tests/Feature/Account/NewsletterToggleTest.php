<?php

namespace Tests\Feature\Account;

use App\Models\EmailChangeRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_suscribe_al_usuario(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);

        $response = $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => true]);

        $response->assertOk();
        $this->assertDatabaseHas('newsletter', [
            'email' => 'yo@ophi.test',
            'user_id' => $user->id,
            'status' => 'subscribed',
        ]);
    }

    public function test_desuscribe_al_usuario(): void
    {
        $user = User::factory()->create(['email' => 'yo@ophi.test']);
        $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => true]);

        $response = $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => false]);

        $response->assertOk();
        $this->assertDatabaseHas('newsletter', [
            'email' => 'yo@ophi.test',
            'status' => 'unsubscribed',
        ]);
    }

    public function test_el_cambio_de_email_arrastra_la_suscripcion(): void
    {
        $user = User::factory()->create(['email' => 'viejo@ophi.test']);
        $this->actingAs($user)->putJson('/api/account/newsletter', ['subscribed' => true]);

        EmailChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => 'nuevo@ophi.test',
            'token' => hash('sha256', 'token-valido'),
            'expires_at' => now()->addHour(),
        ]);

        $this->postJson('/api/account/email/confirm', ['token' => 'token-valido']);

        $this->assertDatabaseHas('newsletter', [
            'user_id' => $user->id,
            'email' => 'nuevo@ophi.test',
        ]);
        $this->assertDatabaseMissing('newsletter', ['email' => 'viejo@ophi.test']);
    }
}
