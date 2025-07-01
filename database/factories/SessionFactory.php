<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\ExamSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SessionFactory extends Factory
{
    protected $model = ExamSession::class;

    public function definition(): array
    {
        return [
            'date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'location_id' => Question::factory(),
        ];
    }
}
