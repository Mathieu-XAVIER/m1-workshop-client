<?php

namespace App\Models;

use Database\Factories\BlocFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property string $nb_questions
 * @property int $quizz_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Question> $questions
 * @property-read int|null $questions_count
 * @property-read Quizz|null $quizz
 * @method static BlocFactory factory($count = null, $state = [])
 * @method static Builder<static>|Bloc newModelQuery()
 * @method static Builder<static>|Bloc newQuery()
 * @method static Builder<static>|Bloc onlyTrashed()
 * @method static Builder<static>|Bloc query()
 * @method static Builder<static>|Bloc whereCreatedAt($value)
 * @method static Builder<static>|Bloc whereDeletedAt($value)
 * @method static Builder<static>|Bloc whereId($value)
 * @method static Builder<static>|Bloc whereName($value)
 * @method static Builder<static>|Bloc whereNbQuestions($value)
 * @method static Builder<static>|Bloc whereQuizzId($value)
 * @method static Builder<static>|Bloc whereUpdatedAt($value)
 * @method static Builder<static>|Bloc withTrashed()
 * @method static Builder<static>|Bloc withoutTrashed()
 * @mixin Eloquent
 */
class Bloc extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nb_questions',
        'quizz_id',
    ];

    public function quizz(): BelongsTo
    {
        return $this->belongsTo(Quizz::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'bloc_question')
            ->using(BlocQuestion::class)
            ->withPivot('skill')
            ->withTimestamps();
    }
}
