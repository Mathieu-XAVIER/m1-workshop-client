<?php

namespace App\Models;

use App\Enums\BlocQuestionSkill;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BlocQuestion extends Pivot
{
    protected $table = 'bloc_question';

    protected $fillable = [
        'bloc_id',
        'question_id',
        'skill',
    ];

    protected $casts = [
        'skill' => BlocQuestionSkill::class,
    ];

    public function bloc(): BelongsTo
    {
        return $this->belongsTo(Bloc::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
