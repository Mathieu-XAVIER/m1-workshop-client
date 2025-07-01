<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blocs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('locale');
            $table->string('nb_questions');
            $table->foreignId('quizz_id')->constrained('quizzs');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocs');
    }
};
