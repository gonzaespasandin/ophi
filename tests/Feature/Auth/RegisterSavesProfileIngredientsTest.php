<?php

namespace Tests\Feature\Auth;

use App\Models\Ingredient;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegisterSavesProfileIngredientsTest extends TestCase
{
    use RefreshDatabase;

    private array $ingredients = [];

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('plans')->insert([
            'id' => 1,
            'name' => 'free',
            'price_per_month' => 0,
            'price_per_year' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (['Gluten', 'Lactosa', 'Maní'] as $name) {
            $this->ingredients[$name] = Ingredient::create(['name' => $name]);
        }
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'terms_and_conditions' => true,
            'name' => 'Gonzalo',
            'email' => 'nuevo@ophi.test',
            'password' => 'Password1',
            'confirm_password' => 'Password1',
        ], $overrides);
    }

    private function mainProfileOf(string $email): Profile
    {
        $user = User::where('email', $email)->firstOrFail();

        return Profile::where('owner_id', $user->id)->where('is_main', true)->firstOrFail();
    }

    public function test_el_registro_guarda_los_ingredientes_elegidos_en_el_perfil_principal(): void
    {
        $elegidos = [
            $this->ingredients['Gluten']->id,
            $this->ingredients['Maní']->id,
        ];

        $response = $this->postJson('/api/register', $this->payload([
            'ingredients' => $elegidos,
        ]));

        $response->assertCreated();

        $guardados = $this->mainProfileOf('nuevo@ophi.test')
            ->ingredients()
            ->pluck('ingredients.id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        sort($elegidos);

        $this->assertSame($elegidos, $guardados);
    }

    public function test_el_registro_sin_ingredientes_crea_el_perfil_principal_vacio(): void
    {
        $response = $this->postJson('/api/register', $this->payload());

        $response->assertCreated();

        $this->assertSame(0, $this->mainProfileOf('nuevo@ophi.test')->ingredients()->count());
    }

    public function test_un_ingrediente_inexistente_rechaza_el_registro_y_no_crea_al_usuario(): void
    {
        $usuariosAntes = User::count();

        $response = $this->postJson('/api/register', $this->payload([
            'ingredients' => [$this->ingredients['Gluten']->id, 999999],
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('ingredients.1');

        $this->assertSame($usuariosAntes, User::count());
        $this->assertDatabaseMissing('users', ['email' => 'nuevo@ophi.test']);
    }

    public function test_el_registro_con_ingredientes_sigue_creando_un_unico_perfil_principal(): void
    {
        $this->postJson('/api/register', $this->payload([
            'ingredients' => [$this->ingredients['Lactosa']->id],
        ]))->assertCreated();

        $user = User::where('email', 'nuevo@ophi.test')->firstOrFail();

        $this->assertSame(1, Profile::where('owner_id', $user->id)->where('is_main', true)->count());
        $this->assertSame('Gonzalo', $this->mainProfileOf('nuevo@ophi.test')->name);
    }
}
