<?php

namespace App\Models;

use Database\Factories\TrainingFactory;
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
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Training> $trainings
 * @property-read int|null $trainings_count
 * @method static TrainingFactory factory($count = null, $state = [])
 * @method static Builder<static>|Training newModelQuery()
 * @method static Builder<static>|Training newQuery()
 * @method static Builder<static>|Training onlyTrashed()
 * @method static Builder<static>|Training query()
 * @method static Builder<static>|Training whereCreatedAt($value)
 * @method static Builder<static>|Training whereDeletedAt($value)
 * @method static Builder<static>|Training whereId($value)
 * @method static Builder<static>|Training whereName($value)
 * @method static Builder<static>|Training whereUpdatedAt($value)
 * @method static Builder<static>|Training withTrashed()
 * @method static Builder<static>|Training withoutTrashed()
 * @mixin Eloquent
 */
class Training extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_training')
            ->withPivot('status')
            ->withTimestamps();
    }
}
