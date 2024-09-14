<?php

namespace App\Models;

use App\Casts\TimeCast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @property Carbon $duration
 */
class Productivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'description',
        'started_at',
        'finished_at',
        'leave_time',
        'day',
    ];

    protected $appends = [
        'duration'
    ];

    protected function casts(): array
    {
        return [
            'day' => 'date',
            'started_at' => TimeCast::class,
            'finished_at' => TimeCast::class,
            'leave_time' => TimeCast::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function scopeForMe(Builder $builder): void
    {
        $builder->where('user_id', auth()->id());
    }

    public function scopeForToday(Builder $builder): void
    {
        $builder->whereDate('day', today());
    }

    public function getDurationAttribute(): Carbon
    {
        $finish = Carbon::createFromTimeString($this->finished_at);
        $started = Carbon::createFromTimeString($this->started_at);
        $leave = Carbon::createFromTimeString($this->leave_time);
        return $finish
            ->subHours($started->hour)
            ->subHours($leave->hour)
            ->subMinutes($started->minute)
            ->subMinutes($leave->minute);
    }
}
