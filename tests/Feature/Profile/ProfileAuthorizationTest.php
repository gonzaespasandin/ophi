<?php

namespace Tests\Feature\Profile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_no_puede_editar_el_perfil_de_otro(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['name' => 'Original']);

        $response = $this->actingAs($intruder)
            ->putJson("/api/profiles/{$profile->id}", ['name' => 'Hackeado']);

        $response->assertNotFound();
        $this->assertSame('Original', $profile->fresh()->name);
    }

    public function test_un_usuario_no_puede_eliminar_el_perfil_de_otro(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create();

        $response = $this->actingAs($intruder)
            ->deleteJson("/api/profiles/{$profile->id}");

        $response->assertNotFound();
        $this->assertDatabaseHas('profiles', ['id' => $profile->id]);
    }

    public function test_el_dueno_si_puede_editar_su_perfil(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['name' => 'Original']);

        $response = $this->actingAs($owner)
            ->putJson("/api/profiles/{$profile->id}", ['name' => 'Nuevo nombre']);

        $response->assertOk();
        $this->assertSame('Nuevo nombre', $profile->fresh()->name);
    }

    public function test_persiste_el_color_del_avatar(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['avatar_color' => null]);

        $response = $this->actingAs($owner)
            ->putJson("/api/profiles/{$profile->id}", ['avatar_color' => '#005B8E']);

        $response->assertOk();
        $this->assertSame('#005B8E', $profile->fresh()->avatar_color);
    }

    public function test_rechaza_un_color_con_formato_invalido(): void
    {
        $owner = User::factory()->create();
        $profile = Profile::factory()->forOwner($owner)->create(['avatar_color' => null]);

        $response = $this->actingAs($owner)
            ->putJson("/api/profiles/{$profile->id}", ['avatar_color' => 'rojo']);

        $response->assertStatus(422);
        $this->assertNull($profile->fresh()->avatar_color);
    }
}
