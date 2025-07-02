<?php

namespace Database\Factories;

use App\Models\Training;
use App\Models\User;
use App\Models\UserTraining;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserTrainingFactory extends Factory
{
    protected $model = UserTraining::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'training_id' => Training::factory(),
        ];
    }
}
