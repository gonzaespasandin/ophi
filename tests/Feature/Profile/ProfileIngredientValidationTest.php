<?php

namespace Tests\Feature\Profile;

use App\Models\Ingredient;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileIngredientValidationTest extends TestCase
{
    use RefreshDatabase;

    private Ingredient $gluten;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gluten = Ingredient::create(['name' => 'Gluten']);
    }

    private function idInexistente(): int
    {
        return (int) Ingredient::max('id') + 999;
    }

    public function test_crear_un_perfil_con_un_ingrediente_inexistente_devuelve_422(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/profiles', [
            'name' => 'Perfil nuevo',
            'ingredients' => [$this->gluten->id, $this->idInexistente()],
        ]);

        // Un 422 no alcanza: el catch de store() ya convertía la excepción de la
        // base en 422, pero devolvía el mensaje de SQL crudo al cliente. Tiene
        // que ser un error de validación, y no puede filtrar nada del motor.
        $response->assertStatus(422)->assertJsonValidationErrors('ingredients.1');
        $this->assertStringNotContainsString('SQLSTATE', (string) $response->getContent());
        $this->assertDatabaseMissing('profiles', ['name' => 'Perfil nuevo']);
    }

    public function test_editar_un_perfil_con_un_ingrediente_inexistente_devuelve_422(): void
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->forOwner($user)->create(['name' => 'Original']);
        $profile->ingredients()->attach($this->gluten->id);

        $response = $this->actingAs($user)->putJson("/api/profiles/{$profile->id}", [
            'ingredients' => [$this->idInexistente()],
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('ingredients.0');
        $this->assertStringNotContainsString('SQLSTATE', (string) $response->getContent());
        $this->assertSame(
            [$this->gluten->id],
            $profile->fresh()->ingredients->pluck('id')->all()
        );
    }

    public function test_crear_un_perfil_con_ingredientes_validos_sigue_funcionando(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/profiles', [
            'name' => 'Perfil nuevo',
            'ingredients' => [$this->gluten->id],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('profiles', ['name' => 'Perfil nuevo']);
    }
}
