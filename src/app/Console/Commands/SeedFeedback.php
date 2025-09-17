<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedFeedback extends Command
{
    protected $signature = 'seed:feedback 
                            {--count=10 : Número de feedbacks a crear}';
    protected $description = 'Crear feedbacks con cantidad específica';

    public function handle()
    {
        config(['seeder.feedback_count' => $this->option('count')]);
        
        $this->call('db:seed', [
            '--class' => 'Database\Seeders\FeedbackSeeder',
            '--force' => true
        ]);
    }
}