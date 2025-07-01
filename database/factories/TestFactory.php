<?php

namespace Database\Factories;

use App\Models\Quizz;
use App\Models\ExamSession;
use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TestFactory extends Factory
{
    protected $model = Test::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'session_id' => ExamSession::factory(),
            'quizz_id' => Quizz::factory(),
        ];
    }
}
