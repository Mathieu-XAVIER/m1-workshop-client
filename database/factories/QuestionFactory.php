<?php

namespace Database\Factories;

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
            'status' => $this->faker->word(),
            'type' => $this->faker->word(),
            'level' => $this->faker->randomNumber(),
            'content' => $this->faker->words(),
            'answer' => $this->faker->words(),
            'locale' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
