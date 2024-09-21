<?php

namespace App\Traits;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait PageWithDayFilter
{
    public string $starts_at;
    public string $ends_at;

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('starts_at')
                    ->afterStateUpdated(fn(?string $state) => $this->loadTable())
                    ->columnSpan(1)
                    ->jalali()
                    ->live(),

                DatePicker::make('ends_at')
                    ->afterStateUpdated(fn(?string $state) => $this->loadTable())
                    ->columnSpan(1)
                    ->jalali()
                    ->live(),

            ])
            ->columns(4);
    }


    public function dayFilter(Builder|HasMany &$query): Builder|HasMany
    {
        if (isset($this->starts_at) and Carbon::canBeCreatedFromFormat($this->starts_at, 'Y-m-d H:i:s')) {
            $query->whereDate('day', '>=', $this->starts_at);
        }
        if (isset($this->ends_at) and Carbon::canBeCreatedFromFormat($this->ends_at, 'Y-m-d H:i:s')) {
            $query->whereDate('day', '<=', $this->ends_at);
        }
        return $query;
    }
}
