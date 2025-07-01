<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\QuestionPassed;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class QuestionPassedFactory extends Factory
{
    protected $model = QuestionPassed::class;

    public function definition(): array
    {
        return [
            'answer' => $this->faker->words(),
            'timer' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'test_id' => Test::factory(),
            'question_id' => Question::factory(),
        ];
    }
}
