<?php

namespace Database\Factories;

use App\Models\Quizz;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class QuizzFactory extends Factory
{
    protected $model = Quizz::class;

    public function definition(): array
    {
        return [
            'subject' => $this->faker->word(),
            'locale' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
