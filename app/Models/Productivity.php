<?php

namespace App\Models;

use App\Casts\TimeCast;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected function casts(): array
    {
        return [
            'day' => 'date',
            'started_at' => TimeCast::class,
            'finished_at' => TimeCast::class,
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
}
