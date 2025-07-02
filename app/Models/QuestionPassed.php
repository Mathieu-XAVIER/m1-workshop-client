<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionPassed extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'question_id',
        'answer',
        'timer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    protected function casts(): array
    {
        return [
            'answer' => 'array',
            'timer' => 'timestamp',
        ];
    }
}
