<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DecrementQteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'decrement:value {model} {field}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will decrement the quantity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = $this->argument('model'); // Get model name from command arguments
        $field = $this->argument('field');   // Get field name from command arguments

        $modelClass = "App\\Models\\" . $model; // Build the model class name

        if (!class_exists($modelClass)) {
            $this->error("Model '$model' not found!");
            return 1;
        }

        $records = $modelClass::all(); // Fetch the first record (modify if needed)
        if($records) {
            foreach($records as $record) {
                if($record->vie == 0) continue;
                $record->decrement($field); // Decrement the specified field
                $this->info("Decremented '{$field}' field in '{$model}' model.");
            }
        }
       

        return 0;
    }
    
}
