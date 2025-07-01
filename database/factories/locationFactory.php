<?php

namespace Database\Factories;

use App\Models\location;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class locationFactory extends Factory
{
    protected $model = location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'adress_line_1' => $this->faker->word(),
            'adress_line_2' => $this->faker->word(),
            'city' => $this->faker->city(),
            'zip_code' => $this->faker->postcode(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
