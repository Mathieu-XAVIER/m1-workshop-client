<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'status',
        'type',
        'level',
        'content',
        'answer',
        'locale',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'answer' => 'array',
        ];
    }
}
