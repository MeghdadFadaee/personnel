<?php

namespace App\Models;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
