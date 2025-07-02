<?php

namespace App\Models;

use Database\Factories\LocationFactory;
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
 * @property string $name
 * @property string $adress_line_1
 * @property string|null $adress_line_2
 * @property string $city
 * @property string $zip_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, ExamSession> $examSessions
 * @property-read int|null $exam_sessions_count
 * @method static LocationFactory factory($count = null, $state = [])
 * @method static Builder<static>|Location newModelQuery()
 * @method static Builder<static>|Location newQuery()
 * @method static Builder<static>|Location onlyTrashed()
 * @method static Builder<static>|Location query()
 * @method static Builder<static>|Location whereAdressLine1($value)
 * @method static Builder<static>|Location whereAdressLine2($value)
 * @method static Builder<static>|Location whereCity($value)
 * @method static Builder<static>|Location whereCreatedAt($value)
 * @method static Builder<static>|Location whereDeletedAt($value)
 * @method static Builder<static>|Location whereId($value)
 * @method static Builder<static>|Location whereName($value)
 * @method static Builder<static>|Location whereUpdatedAt($value)
 * @method static Builder<static>|Location whereZipCode($value)
 * @method static Builder<static>|Location withTrashed()
 * @method static Builder<static>|Location withoutTrashed()
 * @mixin Eloquent
 */
class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'adress_line_1',
        'adress_line_2',
        'city',
        'zip_code',
    ];

    public function examSessions(): HasMany
    {
        return $this->hasMany(ExamSession::class);
    }
}
