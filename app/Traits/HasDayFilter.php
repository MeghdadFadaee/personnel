<?php

namespace App\Traits;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

trait HasDayFilter
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

    public function applyDayFilter(Builder|Relation &$query): Builder|Relation
    {
        if (isset($this->starts_at) and Carbon::canBeCreatedFromFormat($this->starts_at, 'Y-m-d H:i:s')) {
            $query->whereDate('day', '>=', $this->starts_at);
        }
        if (isset($this->ends_at) and Carbon::canBeCreatedFromFormat($this->ends_at, 'Y-m-d H:i:s')) {
            $query->whereDate('day', '<=', $this->ends_at);
        }
        return $query;
    }

    public function setDayFilterProperty($params): void
    {
        if (isset($params['starts_at'])) {
            $this->starts_at = $params['starts_at'];
        }
        if (isset($params['ends_at'])) {
            $this->ends_at = $params['ends_at'];
        }
    }

    public function getResolvedParams(): array
    {
        $params = [];
        if (isset($this->starts_at)) {
            $params['starts_at'] = $this->starts_at;
        }
        if (isset($this->ends_at)) {
            $params['ends_at'] = $this->ends_at;
        }
        return $params;
    }
}
