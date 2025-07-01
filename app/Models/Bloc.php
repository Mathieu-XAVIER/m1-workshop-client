<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bloc extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'locale',
        'nb_questions',
        'quizz_id',
    ];

    public function quizz(): BelongsTo
    {
        return $this->belongsTo(Quizz::class);
    }
}
