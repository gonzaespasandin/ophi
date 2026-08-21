<?php

namespace Tests\Feature\Product;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Caracteriza getIngredientIds() antes de limpiarle el bloque de logging de
 * debugging que arrastraba. El método no tenía ninguna cobertura y lo usa
 * ProductController, así que el contrato queda fijado acá.
 */
class ProductIngredientIdsTest extends TestCase
{
    use RefreshDatabase;

    private Brand $brand;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->brand = Brand::create(['name' => 'Marca de prueba']);
        $this->category = Category::create(['name' => 'Categoría de prueba']);
    }

    private function producto(string $name): Product
    {
        return Product::create([
            'name' => $name,
            'brand_id' => $this->brand->id,
            'category_id' => $this->category->id,
        ]);
    }

    public function test_devuelve_los_ids_de_los_ingredientes_del_producto(): void
    {
        $gluten = Ingredient::create(['name' => 'Gluten']);
        $mani = Ingredient::create(['name' => 'Maní']);

        $product = $this->producto('Galletitas');
        $product->ingredients()->attach([$gluten->id, $mani->id]);

        $this->assertEqualsCanonicalizing(
            [$gluten->id, $mani->id],
            $product->getIngredientIds()
        );
    }

    public function test_devuelve_un_array_vacio_si_el_producto_no_tiene_ingredientes(): void
    {
        $product = $this->producto('Agua mineral');

        $this->assertSame([], $product->getIngredientIds());
    }
}
