<?php

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('status', [
                QuestionStatus::PENDING->value,
                QuestionStatus::APPROVED->value,
                QuestionStatus::REJECTED->value,
            ])->default(QuestionStatus::PENDING->value);
            $table->enum('type', [
                QuestionType::MULTIPLE_CHOICE->value,
                QuestionType::TRUE_FALSE->value,
                QuestionType::FILL_IN_THE_BLANK->value,
                QuestionType::SHORT_ANSWER->value,
                QuestionType::LONG_ANSWER->value,
            ]);
            $table->integer('level');
            $table->json('content');
            $table->json('answer');
            $table->string('locale')->default('fr');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
