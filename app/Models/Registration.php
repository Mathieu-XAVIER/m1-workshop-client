<?php

namespace App\Models;

use Database\Factories\RegistrationFactory;
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
 * @property int $user_id
 * @property int $exam_session_id
 * @property bool $no_show
 * @property int $registered_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ExamSession|null $examSession
 * @property-read User|null $registeredBy
 * @property-read User|null $user
 * @method static RegistrationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Registration newModelQuery()
 * @method static Builder<static>|Registration newQuery()
 * @method static Builder<static>|Registration query()
 * @method static Builder<static>|Registration whereCreatedAt($value)
 * @method static Builder<static>|Registration whereExamSessionId($value)
 * @method static Builder<static>|Registration whereId($value)
 * @method static Builder<static>|Registration whereNoShow($value)
 * @method static Builder<static>|Registration whereRegisteredBy($value)
 * @method static Builder<static>|Registration whereUpdatedAt($value)
 * @method static Builder<static>|Registration whereUserId($value)
 * @mixin Eloquent
 */
class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_session_id',
        'no_show',
        'registered_by',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    protected function casts(): array
    {
        return [
            'no_show' => 'boolean',
        ];
    }
}
