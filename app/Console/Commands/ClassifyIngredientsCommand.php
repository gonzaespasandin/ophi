<?php

namespace App\Console\Commands;

use App\Classifier\IngredientClassifier;
use App\Models\Ingredient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClassifyIngredientsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ingredients:classify {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It goes through some or all the ingredients and classify them into different groups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();

        if ($this->option('fresh')) {
            $this->line('  Deleting all ingredient relations...');
            DB::table('ingredient_ingredient')->delete();
            $this->info('  DONE');
            $this->newLine();
        }

        $this->line('  Running classifier...');
        IngredientClassifier::run($this);
        $this->line('  All relations done');
    }
}
