<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Google Sheets: https://docs.google.com/spreadsheets/d/1LDv_qH4FuhV0LQTiN5MJDcJnZP0sCP3rvgly8dti_IY/edit?usp=sharing
         * array_map: https://www.php.net/manual/en/function.array-map.php
         * file: https://www.php.net/manual/en/function.file.php
         * array_walk: https://www.php.net/manual/en/function.array-walk.php
         * array_combine: https://www.php.net/manual/en/function.array-combine.php
         * array_shift: https://www.php.net/manual/en/function.array-shift.php
         * */
        $csv = array_map('str_getcsv', file(__DIR__ . '/../csv/ingredients.csv'));
        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv); # remove column header

        DB::table('ingredients')->insert($csv);
    }
}
