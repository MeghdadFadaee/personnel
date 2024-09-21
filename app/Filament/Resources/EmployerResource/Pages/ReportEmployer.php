<?php

namespace App\Filament\Resources\EmployerResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\EmployerResource;
use App\Traits\PageWithDayFilter;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers;
use Illuminate\Support\Number;

class ReportEmployer extends BaseListRecords implements HasForms
{
    use InteractsWithForms;
    use PageWithDayFilter;

    protected static string $resource = EmployerResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
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
                    ->columnSpan(2)
                    ->maxLength(255),

                TextInput::make('total_work_duration')
                    ->readOnly(),

                TextInput::make('total_salaries')
                    ->prefix(trans('toman'))
                    ->default(0)
                    ->readOnly(),
            ]);
    }

    public function table(Table $table): Table
    {
        $table = parent::table(table: $table);
        $columns = Arr::except($table->getColumns(), ['users.full_name']);

        return $table
            ->columns([
                ...$columns,

                TextColumn::make('total_work_duration')
                    ->copyable()
                    ->formatStateUsing(
                        fn($state) => Carbon::createFromTime()
                            ->addSeconds((int) $state)
                            ->format('H:i:s')
                    )
                    ->tooltip(
                        fn($state) => Carbon::createFromTime()
                            ->addSeconds((int) $state)
                            ->diff('00:00:00')
                            ->forHumans()
                    )
                    ->summarize([
                        Summarizers\Summarizer::make()
                            ->label(trans('Sum'))
                            ->formatStateUsing(fn($state) => $this->getTotalWorkDurationSum()),
                    ]),


                TextColumn::make('total_salaries')
                    ->numeric()
                    ->copyable()
                    ->sortable(false)
                    ->prefix(trans('toman'))
                    ->summarize([
                        Summarizers\Summarizer::make()
                            ->label(trans('Sum'))
                            ->formatStateUsing(fn($state) => $this->getTotalSalariesSum()),
                    ]),
            ])
            ->actions([])
            ->toggleableAll()
            ->modifyQueryUsing(fn(Builder $query) => $this->modifyTableQuery($query));
    }

    public function modifyTableQuery(Builder $query): Builder
    {
        $query->with('users');
        $query->with([
            'productivities' => fn($builder) => $this->dayFilter($builder),
        ]);

        $query->withSum([
            "productivities AS total_work_duration" => function (Builder $builder) {
                $builder->select(DB::raw('SUM(TIME_TO_SEC(finished_at) - TIME_TO_SEC(started_at) - TIME_TO_SEC(leave_time))'));
                $this->dayFilter($builder);
            },
        ], 'total_work_duration');

        return $query;
    }

    public function getTotalSalariesSum(): string
    {
        $projects = $this->table->getQuery()->get();
        return Number::format($projects->sum('total_salaries'), locale: config('app.locale'));
    }

    public function getTotalWorkDurationSum(): string
    {
        $projects = $this->table->getQuery()->get();

        $totalDuration = Carbon::createFromTime();
        foreach ($projects as $project) {
            if (!empty($project->total_work_duration)) {
                $totalDuration->addSeconds((int) $project->total_work_duration);
            }
        }
        return $totalDuration->diff('00:00:00')->forHumans();
    }
}
