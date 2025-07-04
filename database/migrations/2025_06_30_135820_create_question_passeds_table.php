<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('question_passeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('tests');
            $table->foreignId('question_id')->constrained('questions');
            $table->json('answer');
            $table->timestamp('timer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_passeds');
    }
};
