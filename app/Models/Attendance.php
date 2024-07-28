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
        'entered_at',
        'exited_at',
        'reduce',
        'vacation',
        'home_work',
        'day',
    ];
    protected $casts = [
        'day' => 'date',
        'entered_at' => TimeCast::class,
        'exited_at' => TimeCast::class,
        'reduce' => TimeCast::class,
        'vacation' => TimeCast::class,
        'home_work' => TimeCast::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForToday(Builder $builder): void
    {
        $builder->whereDate('day', today());
    }
}
