<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use App\Traits\HasDayFilter;
use Carbon\Carbon;
use Filament\Tables\Columns;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\Summarizers;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class UserProductivities extends TableWidget
{
    use HasDayFilter;

    public null|User $record = null;
    protected int|string|array $columnSpan = 'full';

    protected $listeners = [
        'updateUserProductivities' => '$refresh',
        'setUserProductivitiesFilters',
    ];

    public function setUserProductivitiesFilters(array $params): void
    {
        $this->setDayFilterProperty($params);
        $this->dispatch('updateUserProductivities');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return trans('User Productivities');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->searchable(false)
            ->modelLabel(trans('productivity'))
            ->pluralModelLabel(trans('productivities'))
            ->groups([
                Group::make('employer.title')
                    ->label(trans('employer'))
                    ->collapsible(),

                Group::make('employer.title')
                    ->label(trans('employer'))
                    ->collapsible(),
            ])
            ->defaultGroup('employer.title')
            ->columns([
                Columns\TextColumn::make('employer.title'),

                Columns\TextColumn::make('day')
                    ->jalaliDate(),

                Columns\TextColumn::make('description'),

                Columns\TextColumn::make('total_work_duration')
                    ->label(trans('Total work duration'))
                    ->formatStateUsing(fn($state) => secondsToTime($state))
                    ->tooltip(fn($state) => secondsToTimeForHumans($state))
                    ->summarize([
                        Summarizers\Sum::make()
                            ->formatStateUsing(fn($state) => secondsToTime($state))
                    ]),

                Columns\TextColumn::make('user.hourly_salary')
                    ->label(trans('Hourly salary'))
                    ->suffix(' '.trans('toman'))
                    ->copyable()
                    ->numeric(),

                Columns\TextColumn::make('total_salaries')
                    ->summarize([
                        Summarizers\Sum::make()
                            ->suffix(' '.trans('toman'))
                    ])
                    ->suffix(' '.trans('toman'))
                    ->copyable()
                    ->numeric(),

            ])
            ->headerActions([
            ])
            ->actions([
            ]);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $query = $this->record->productivity()->getQuery();
        $this->selectSecond($query);
        $this->withTotalWork($query);
        $this->withTotalSalary($query);
        $this->applyDayFilter($query);
        return $query;
    }

    protected function selectSecond(Builder &$query)
    {
        $query = $query->selectRaw('*');
        $query = $query->selectRaw('TIME_TO_SEC(started_at) as started_at_sec');
        $query = $query->selectRaw('TIME_TO_SEC(finished_at) as finished_at_sec');
        $query = $query->selectRaw('TIME_TO_SEC(leave_time) as leave_time_sec');
    }

    protected function withTotalWork(&$query): void
    {
        $query = $query->withSum('user as total_work_duration', DB::raw('finished_at_sec - started_at_sec - leave_time_sec'));
    }

    protected function withTotalSalary(&$query): void
    {
        $query = $query->withSum('user as total_salaries', DB::raw('((total_work_duration / 60) / 60) * hourly_salary'));
    }
}
