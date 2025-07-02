<?php

namespace Database\Factories;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'status' => $this->faker->randomElement(QuestionStatus::cases()),
            'type' => $this->faker->randomElement(QuestionType::cases()),
            'level' => $this->faker->randomNumber(),
            'content' => $this->faker->words(),
            'answer' => $this->faker->words(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
