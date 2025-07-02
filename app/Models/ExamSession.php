<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property Carbon $date
 * @property int $location_id
 * @property int|null $capacity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Location|null $location
 * @property-read Collection<int, Registration> $registrations
 * @property-read int|null $registrations_count
 * @property-read Collection<int, Test> $tests
 * @property-read int|null $tests_count
 * @method static Builder<static>|ExamSession newModelQuery()
 * @method static Builder<static>|ExamSession newQuery()
 * @method static Builder<static>|ExamSession onlyTrashed()
 * @method static Builder<static>|ExamSession query()
 * @method static Builder<static>|ExamSession whereCapacity($value)
 * @method static Builder<static>|ExamSession whereCreatedAt($value)
 * @method static Builder<static>|ExamSession whereDate($value)
 * @method static Builder<static>|ExamSession whereDeletedAt($value)
 * @method static Builder<static>|ExamSession whereId($value)
 * @method static Builder<static>|ExamSession whereLocationId($value)
 * @method static Builder<static>|ExamSession whereName($value)
 * @method static Builder<static>|ExamSession whereUpdatedAt($value)
 * @method static Builder<static>|ExamSession withTrashed()
 * @method static Builder<static>|ExamSession withoutTrashed()
 * @mixin Eloquent
 */
class ExamSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'date',
        'location_id',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'registered_by');
    }

    public function tests(): HasMany
    {
        return $this->hasMany(Test::class, 'session_id');
    }
}
