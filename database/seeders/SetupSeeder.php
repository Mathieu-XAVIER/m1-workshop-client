<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class SetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Question::factory()->count(10)->create();
    }
}
