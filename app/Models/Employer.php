<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;


/**
 * @property Productivity[] $productivities
 */
class Employer extends Model
{
    use HasFactory, softDeletes;

    protected $fillable = [
        'title',
    ];

    protected $appends = [
        'total_salaries',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function productivities(): HasMany
    {
        return $this->hasMany(Productivity::class);
    }

    public function getTotalSalariesAttribute(): int
    {
        /** @var Collection $salaries */
        $salaries = $this->users->pluck('hourly_salary', 'id');

        $totalSalaries = 0;
        foreach ($this->productivities as $productivity) {
            $duration = $productivity->duration->hour + ($productivity->duration->minute / 60);
            $totalSalaries += $salaries->get($productivity->user_id) * $duration;
        }

        return (int) $totalSalaries;
    }
}
