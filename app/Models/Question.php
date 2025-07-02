<?php

namespace App\Models;

use App\Enums\QuestionStatus;
use App\Enums\QuestionType;
use Database\Factories\QuestionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $title
 * @property QuestionStatus $status
 * @property QuestionType $type
 * @property int $level
 * @property array<array-key, mixed> $content
 * @property array<array-key, mixed> $answer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Bloc> $blocs
 * @property-read int|null $blocs_count
 * @method static QuestionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Question newModelQuery()
 * @method static Builder<static>|Question newQuery()
 * @method static Builder<static>|Question onlyTrashed()
 * @method static Builder<static>|Question query()
 * @method static Builder<static>|Question whereAnswer($value)
 * @method static Builder<static>|Question whereContent($value)
 * @method static Builder<static>|Question whereCreatedAt($value)
 * @method static Builder<static>|Question whereDeletedAt($value)
 * @method static Builder<static>|Question whereId($value)
 * @method static Builder<static>|Question whereLevel($value)
 * @method static Builder<static>|Question whereStatus($value)
 * @method static Builder<static>|Question whereTitle($value)
 * @method static Builder<static>|Question whereType($value)
 * @method static Builder<static>|Question whereUpdatedAt($value)
 * @method static Builder<static>|Question withTrashed()
 * @method static Builder<static>|Question withoutTrashed()
 * @mixin Eloquent
 */
class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'status',
        'type',
        'level',
        'content',
        'answer',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'answer' => 'array',
            'status' => QuestionStatus::class,
            'type' => QuestionType::class,
        ];
    }

    public function blocs(): BelongsToMany
    {
        return $this->belongsToMany(Bloc::class, 'bloc_question');
    }
}
