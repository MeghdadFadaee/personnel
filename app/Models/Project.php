<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'employer_id',
        'title',
        'amount',
        'fee',
        'day',
    ];

    protected $appends = [
        'amount_done',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function performances(): HasMany
    {
        return $this->hasMany(Performance::class);
    }

    public function getAmountDoneAttribute(): int
    {
        return $this->performances()->sum('completed_count');
    }

    public function scopeHasRemaining(Builder $query): void
    {
        $query->whereRaw('projects.amount > (
            SELECT COALESCE(SUM(completed_count), 0)
            FROM performances
            WHERE performances.project_id = projects.id
        )');
    }
}
