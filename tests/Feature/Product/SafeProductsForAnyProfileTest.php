<?php

namespace Tests\Feature\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafeProductsForAnyProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Brand $brand;
    private Category $category;
    private array $ingredients = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Ids 1, 2 and 3 belong to the three big groups and are deliberately
        // skipped when climbing the hierarchy, so no test ingredient may take them.
        foreach (['Intolerancias', 'Alergias', 'Dietas especiales'] as $bigGroup) {
            Ingredient::create(['name' => $bigGroup]);
        }

        $this->user = User::factory()->create();
        $this->brand = Brand::create(['name' => 'Marca de prueba']);
        $this->category = Category::create(['name' => 'Categoría de prueba']);

        foreach (['Gluten', 'Maní', 'Colorante', 'Arroz', 'Harina de trigo'] as $name) {
            $this->ingredients[$name] = Ingredient::create(['name' => $name]);
        }

        // "Harina de trigo" lives under "Gluten": avoiding the group avoids the child.
        $this->ingredients['Gluten']->children()->attach($this->ingredients['Harina de trigo']->id);
    }

    private function profileAvoiding(string $name, array $ingredientNames): Profile
    {
        $profile = Profile::factory()->forOwner($this->user)->create(['name' => $name]);

        $profile->ingredients()->attach(
            collect($ingredientNames)->map(fn ($n) => $this->ingredients[$n]->id)->all()
        );

        return $profile;
    }

    private function productWith(string $name, array $ingredientNames): Product
    {
        $product = Product::create([
            'name' => $name,
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
        ]);

        $product->ingredients()->attach(
            collect($ingredientNames)->map(fn ($n) => $this->ingredients[$n]->id)->all()
        );

        return $product;
    }

    public function test_devuelve_una_coleccion_vacia_para_un_invitado(): void
    {
        $result = ProductService::getProductsSafeForAnyProfile();

        $this->assertTrue($result->isEmpty());
    }

    public function test_devuelve_una_coleccion_vacia_si_el_usuario_no_tiene_perfiles(): void
    {
        $this->actingAs($this->user);

        $result = ProductService::getProductsSafeForAnyProfile();

        $this->assertTrue($result->isEmpty());
    }

    public function test_descarta_los_productos_que_ningun_perfil_puede_comer(): void
    {
        $this->profileAvoiding('Lucía', ['Gluten', 'Colorante']);
        $this->profileAvoiding('Sofía', ['Maní', 'Colorante']);

        $this->productWith('Arroz integral', ['Arroz']);
        $this->productWith('Caramelos con colorante', ['Colorante']);
        $this->productWith('Barra de trigo y maní', ['Harina de trigo', 'Maní']);

        $this->actingAs($this->user);

        $names = ProductService::getProductsSafeForAnyProfile()->pluck('name')->all();

        $this->assertContains('Arroz integral', $names);
        $this->assertNotContains('Caramelos con colorante', $names);
        $this->assertNotContains('Barra de trigo y maní', $names);
    }

    public function test_anota_cada_producto_con_los_perfiles_para_los_que_es_apto(): void
    {
        $lucia = $this->profileAvoiding('Lucía', ['Gluten']);
        $sofia = $this->profileAvoiding('Sofía', ['Maní']);

        $this->productWith('Turrón de maní', ['Maní']);

        $this->actingAs($this->user);

        $turron = ProductService::getProductsSafeForAnyProfile()->firstWhere('name', 'Turrón de maní');

        $this->assertSame([$lucia->id], $turron->safe_for_profile_ids);
        $this->assertNotContains($sofia->id, $turron->safe_for_profile_ids);
    }

    public function test_respeta_la_jerarquia_de_ingredientes(): void
    {
        $lucia = $this->profileAvoiding('Lucía', ['Gluten']);
        $sofia = $this->profileAvoiding('Sofía', ['Maní']);

        $this->productWith('Galletas de trigo', ['Harina de trigo']);

        $this->actingAs($this->user);

        $galletas = ProductService::getProductsSafeForAnyProfile()->firstWhere('name', 'Galletas de trigo');

        // Lucía avoids "Gluten", never "Harina de trigo" directly.
        $this->assertSame([$sofia->id], $galletas->safe_for_profile_ids);
        $this->assertNotContains($lucia->id, $galletas->safe_for_profile_ids);
    }

    public function test_prioriza_los_productos_aptos_para_todo_el_hogar(): void
    {
        $this->profileAvoiding('Lucía', ['Gluten']);
        $this->profileAvoiding('Sofía', ['Maní']);

        $this->productWith('Turrón de maní', ['Maní']);
        $this->productWith('Galletas de trigo', ['Harina de trigo']);
        $this->productWith('Arroz integral', ['Arroz']);

        $this->actingAs($this->user);

        $result = ProductService::getProductsSafeForAnyProfile();

        $this->assertSame('Arroz integral', $result->first()->name);
    }

    public function test_un_perfil_sin_restricciones_puede_comer_cualquier_cosa(): void
    {
        $lucia = $this->profileAvoiding('Lucía', ['Gluten']);
        $pedro = $this->profileAvoiding('Pedro', []);

        $this->productWith('Galletas de trigo', ['Harina de trigo']);

        $this->actingAs($this->user);

        $galletas = ProductService::getProductsSafeForAnyProfile()->firstWhere('name', 'Galletas de trigo');

        $this->assertSame([$pedro->id], $galletas->safe_for_profile_ids);
        $this->assertNotContains($lucia->id, $galletas->safe_for_profile_ids);
    }

    public function test_expone_la_marca_y_la_foto_que_necesita_la_tarjeta(): void
    {
        $this->profileAvoiding('Lucía', ['Gluten']);

        $arroz = $this->productWith('Arroz integral', ['Arroz']);
        $arroz->update(['img' => 'arroz.jpg', 'img_alt' => 'Paquete de arroz']);

        $this->actingAs($this->user);

        $result = ProductService::getProductsSafeForAnyProfile()->firstWhere('name', 'Arroz integral')->toArray();

        $this->assertSame('arroz.jpg', $result['img']);
        $this->assertSame('Paquete de arroz', $result['img_alt']);
        $this->assertSame('Marca de prueba', $result['brand']['name']);
        $this->assertArrayHasKey('safe_for_profile_ids', $result);
    }
}
