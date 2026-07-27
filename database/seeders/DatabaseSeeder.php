<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            IngredientSeeder::class,
            InsCodesSeeder::class,
            UserSeeder::class,
            ProfileSeeder::class,
            IngredientProfileSeeder::class,
            IngredientHasIngredientSeeder::class,
            PlanSeeder::class,
            UserPlanSeeder::class,
            // Product/catalog data comes from products:import-normalized-json in production.
            // ImportProductsFromCsvSeeder::class,
            // CategorySeeder::class,
            // BrandSeeder::class,
            // ProductSeeder::class,
            // IngredientProductSeeder::class,
            // BarcodeSuggestionSeeder::class,
        ]);
    }
}
