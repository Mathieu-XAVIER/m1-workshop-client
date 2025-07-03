<?php

namespace Database\Factories;

use App\Models\Registration;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RegistrationFactory extends Factory
{
    protected $model = Registration::class;

    public function definition(): array
    {
        return [
            'no_show' => $this->faker->boolean(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => User::factory(),
            'exam_session_id' => ExamSession::factory(),
            'registered_by' => User::factory(),
        ];
    }
}
