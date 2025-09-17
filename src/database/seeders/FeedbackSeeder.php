<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feedback;

class FeedbackSeeder extends Seeder
{
    public function run()
    {
        $count = config('seeder.feedback_count', 10);
        Feedback::factory()->count($count)->create();
        $this->command->info("S'han creat $count feedbacks.");
    }
}