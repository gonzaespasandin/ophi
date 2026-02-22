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
            ImportProductsFromCsvSeeder::class,
            UserSeeder::class,
            // IngredientSeeder::class, (ahhora vienen del ImportProductsFromCsvSeeder)
            // CategorySeeder::class, (ahhora vienen del ImportProductsFromCsvSeeder)
            // BrandSeeder::class, (ahhora vienen del ImportProductsFromCsvSeeder)
            // ProductSeeder::class, (ahhora vienen del ImportProductsFromCsvSeeder)
            ProfileSeeder::class,
            // IngredientProductSeeder::class,  (ahhora vienen del ImportProductsFromCsvSeeder)
            IngredientProfileSeeder::class,
            IngredientHasIngredientSeeder::class,
            PlanSeeder::class,
            UserPlanSeeder::class,
            BarcodeSuggestionSeeder::class,
        ]);
    }
}
