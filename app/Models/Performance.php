<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_id',
        'completed_count',
    ];

    protected function casts(): array
    {
        return [
            'day' => 'date',
        ];
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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
