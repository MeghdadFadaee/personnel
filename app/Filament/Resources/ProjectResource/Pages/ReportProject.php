<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\ProjectResource;
use App\Traits\PageWithDayFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ReportProject extends BaseListRecords implements HasForms
{
    use InteractsWithForms;
    use PageWithDayFilter;

    protected static string $resource = ProjectResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.resources.reports.has-filter-form';

    protected function getForms(): array
    {
        return [
            'form',
            'filterForm',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),

                TextInput::make('amount')
                    ->integer()
                    ->default(0),

                TextInput::make('performances_sum_completed_count')
                    ->readOnly()
                    ->default(0),

                TextInput::make('fee')
                    ->suffix(trans('toman'))
                    ->integer()
                    ->nullable()
                    ->default(0),

                TextInput::make('total_salaries')
                    ->suffix(trans('toman'))
                    ->readOnly()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        $table = parent::table(table: $table);
        $columns = Arr::except($table->getColumns(), ['users.full_name', 'employer.title']);

        return $table
            ->columns([
                ...$columns,

                TextColumn::make('total_salaries')
                    ->summarize([
                        Summarizers\Sum::make()
                            ->suffix(' '.trans('toman'))
                    ])
                    ->suffix(' '.trans('toman'))
                    ->copyable()
                    ->numeric(),

                TextColumn::make('performances_sum_completed_count')
                    ->numeric(),
            ])
            ->actions([])
            ->toggleableAll()
            ->modifyQueryUsing(fn(Builder $query) => $this->modifyTableQuery($query));
    }

    public function modifyTableQuery(Builder $query): Builder
    {
        $query->withSum([
            'performances' => fn($builder) => $this->dayFilter($builder)
        ], 'completed_count');

        $query->withSum([
            'performances AS total_salaries' => function (Builder $builder) {
                $builder->select(DB::raw('SUM(completed_count * fee)'));
                $this->dayFilter($builder);
            }
        ], 'total_salaries');

        return $query;
    }
}
