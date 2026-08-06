<?php

namespace Tests\Feature\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MainProfileProtectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_se_puede_eliminar_el_perfil_principal(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->main()->forOwner($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/profiles/{$profile->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('profiles', ['id' => $profile->id]);
    }

    public function test_si_se_puede_eliminar_un_perfil_familiar(): void
    {
        $user = User::factory()->create();
        Profile::factory()->main()->forOwner($user)->create();
        $familiar = Profile::factory()->forOwner($user)->create();

        $response = $this->actingAs($user)->deleteJson("/api/profiles/{$familiar->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('profiles', ['id' => $familiar->id]);
    }
}
