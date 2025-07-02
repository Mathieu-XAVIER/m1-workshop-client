<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'quizz_id',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class, 'session_id');
    }

    public function quizz(): BelongsTo
    {
        return $this->belongsTo(Quizz::class, 'quizz_id');
    }
}
