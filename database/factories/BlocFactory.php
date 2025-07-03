<?php

namespace Database\Factories;

use App\Models\Bloc;
use App\Models\Quizz;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BlocFactory extends Factory
{
    protected $model = Bloc::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'nb_questions' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'quizz_id' => Quizz::factory(),
        ];
    }
}
