<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'location_id',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'location_id');
    }

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }
}
