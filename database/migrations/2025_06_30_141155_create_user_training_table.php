<?php

use App\Enums\TrainingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_training', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('training_id')->constrained('trainings');
            $table->enum('status', [
                TrainingStatus::DONE->value,
                TrainingStatus::TODO->value,
            ])->default(TrainingStatus::TODO->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_training');
    }
};
