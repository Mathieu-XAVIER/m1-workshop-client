<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
