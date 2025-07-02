<?php

namespace App\Models;

use Database\Factories\UserTrainingFactory;
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
 * @property int $training_id
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Training|null $training
 * @property-read User|null $user
 * @method static UserTrainingFactory factory($count = null, $state = [])
 * @method static Builder<static>|UserTraining newModelQuery()
 * @method static Builder<static>|UserTraining newQuery()
 * @method static Builder<static>|UserTraining query()
 * @method static Builder<static>|UserTraining whereCreatedAt($value)
 * @method static Builder<static>|UserTraining whereId($value)
 * @method static Builder<static>|UserTraining whereStatus($value)
 * @method static Builder<static>|UserTraining whereTrainingId($value)
 * @method static Builder<static>|UserTraining whereUpdatedAt($value)
 * @method static Builder<static>|UserTraining whereUserId($value)
 * @mixin Eloquent
 */
class UserTraining extends Model
{
    use HasFactory;

    protected $table = 'user_training';

    protected $fillable = [
        'user_id',
        'training_id',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }
}
