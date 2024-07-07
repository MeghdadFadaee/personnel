<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'started_at',
        'finished_at',
        'reduce',
        'vacation',
        'home_work',
        'day',
    ];
    protected $casts = [
        'day' => 'date',
        'started_at' => TimeCast::class,
        'finished_at' => TimeCast::class,
        'reduce' => TimeCast::class,
        'vacation' => TimeCast::class,
        'home_work' => TimeCast::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForToDay(Builder $builder): void
    {
        $builder->whereDate('day', today());
    }
}
