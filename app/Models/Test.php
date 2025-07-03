<?php

namespace App\Models;

use Database\Factories\TestFactory;
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
 * @property int $exam_session_id
 * @property int $quizz_id
 * @property int $user_id
 * @property int $nb_pause
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Quizz|null $quizz
 * @property-read ExamSession|null $session
 * @method static TestFactory factory($count = null, $state = [])
 * @method static Builder<static>|Test newModelQuery()
 * @method static Builder<static>|Test newQuery()
 * @method static Builder<static>|Test query()
 * @method static Builder<static>|Test whereCreatedAt($value)
 * @method static Builder<static>|Test whereExamSessionId($value)
 * @method static Builder<static>|Test whereId($value)
 * @method static Builder<static>|Test whereNbPause($value)
 * @method static Builder<static>|Test whereQuizzId($value)
 * @method static Builder<static>|Test whereUpdatedAt($value)
 * @method static Builder<static>|Test whereUserId($value)
 * @mixin Eloquent
 */
class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'quizz_id',
        'user_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function quizz(): BelongsTo
    {
        return $this->belongsTo(Quizz::class, 'quizz_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

