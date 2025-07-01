<?php

use App\Enums\BlocQuestionSkill;
use App\Models\Bloc;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bloc_question', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bloc_id')
                ->constrained('blocs')
                ->onDelete('cascade');
            $table->foreignId('question_id')
                ->constrained('questions')
                ->onDelete('cascade');
            $table->enum('skill', [
                BlocQuestionSkill::SPEAKING->value,
                BlocQuestionSkill::WRITING->value,
                BlocQuestionSkill::READING->value,
                BlocQuestionSkill::LISTENING->value,
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloc_question');
    }
};
