<?php

namespace App\Traits;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $hourly_salary
 * @property int $hourly_penalty
 * @property int $total_hourly_salaries
 * @property int $total_hourly_penalties
 * @property int $total_project_salaries
 * @property int $total_salaries
 */
trait SalaryCalculator
{
    public function getTotalHourlySalariesAttribute(): int
    {
        return $this->hourly_salary * ($this->total_worked_all_duration / 60 / 60);
    }

    public function getTotalHourlyPenaltiesAttribute(): int
    {
        return $this->hourly_penalty * ($this->total_delay_duration / 60 / 60);
    }

    public function getTotalProjectSalariesAttribute(): int
    {
        $performance = $this->performance()->with('project')->get();
        return $performance->sum(fn($record) => $record->completed_count * $record->project->fee);
    }

    public function getTotalSalariesAttribute(): int
    {
        return $this->total_hourly_salaries + $this->total_project_salaries - $this->total_hourly_penalties;
    }
}
