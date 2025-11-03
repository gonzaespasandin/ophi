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
            UserSeeder::class,
            IngredientSeeder::class,
            ProductSeeder::class,
            ProfileSeeder::class,
            IngredientProductSeeder::class,
            IngredientProfileSeeder::class,
        ]);
    }
}
