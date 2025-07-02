<?php

namespace App\Models;

use Database\Factories\QuestionPassedFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property int $test_id
 * @property int $question_id
 * @property array<array-key, mixed> $answer
 * @property int $timer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Question|null $question
 * @property-read Test|null $test
 * @method static QuestionPassedFactory factory($count = null, $state = [])
 * @method static Builder<static>|QuestionPassed newModelQuery()
 * @method static Builder<static>|QuestionPassed newQuery()
 * @method static Builder<static>|QuestionPassed query()
 * @method static Builder<static>|QuestionPassed whereAnswer($value)
 * @method static Builder<static>|QuestionPassed whereCreatedAt($value)
 * @method static Builder<static>|QuestionPassed whereId($value)
 * @method static Builder<static>|QuestionPassed whereQuestionId($value)
 * @method static Builder<static>|QuestionPassed whereTestId($value)
 * @method static Builder<static>|QuestionPassed whereTimer($value)
 * @method static Builder<static>|QuestionPassed whereUpdatedAt($value)
 * @mixin Eloquent
 */
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
