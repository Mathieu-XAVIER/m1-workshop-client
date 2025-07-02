<?php

namespace App\Models;

use Database\Factories\QuizzFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $subject
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Bloc> $blocs
 * @property-read int|null $blocs_count
 * @method static QuizzFactory factory($count = null, $state = [])
 * @method static Builder<static>|Quizz newModelQuery()
 * @method static Builder<static>|Quizz newQuery()
 * @method static Builder<static>|Quizz onlyTrashed()
 * @method static Builder<static>|Quizz query()
 * @method static Builder<static>|Quizz whereCreatedAt($value)
 * @method static Builder<static>|Quizz whereDeletedAt($value)
 * @method static Builder<static>|Quizz whereId($value)
 * @method static Builder<static>|Quizz whereSubject($value)
 * @method static Builder<static>|Quizz whereUpdatedAt($value)
 * @method static Builder<static>|Quizz withTrashed()
 * @method static Builder<static>|Quizz withoutTrashed()
 * @mixin Eloquent
 */
class Quizz extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'locale',
    ];

    public function blocs(): HasMany
    {
        return $this->hasMany(Bloc::class);
    }
}
