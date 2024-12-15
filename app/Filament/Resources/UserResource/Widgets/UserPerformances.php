<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use App\Traits\HasDayFilter;
use Filament\Tables\Columns;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class UserPerformances extends TableWidget
{
    use HasDayFilter;

    public null|User $record = null;
    protected int|string|array $columnSpan = 'full';

    protected $listeners = [
        'updateUserPerformances' => '$refresh',
        'setUserPerformancesFilters',
    ];

    public function setUserPerformancesFilters(array $params): void
    {
        $this->setDayFilterProperty($params);
        $this->dispatch('updateUserPerformances');
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return trans('User Performances');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->searchable(false)
            ->modelLabel(trans('performance'))
            ->pluralModelLabel(trans('performances'))
            ->groups([
                Group::make('project.title')
                    ->label(trans('project'))
                    ->collapsible(),
            ])
            ->defaultGroup('project.title')
            ->columns([
                Columns\TextColumn::make('project.title'),

                Columns\TextColumn::make('day')
                    ->jalaliDate(),

                Columns\TextColumn::make('completed_count')
                    ->summarize([
                        Summarizers\Sum::make()
                    ])
                    ->copyable()
                    ->numeric(),

                Columns\TextColumn::make('project.fee')
                    ->label(trans('fee'))
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
        $query = $this->record->performance()->with('project');
        $this->withTotalSalary($query);
        $this->applyDayFilter($query);
        return $query->getQuery();
    }

    protected function withTotalSalary(&$query): void
    {
        $query = $query->withSum('project as total_salaries', DB::raw('fee * completed_count'));
    }
}
