<?php

namespace App\Filament\Resources\EmployerResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\EmployerResource;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ReportEmployer extends BaseListRecords implements HasForms
{
    use InteractsWithForms;

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('total_work_duration')
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
                    ->time()
                    ->tooltip(fn($state) => Carbon::make($state)?->diff('00:00:00')->forHumans())
                    ->copyable(),


                TextColumn::make('total_salaries')
                    ->prefix(trans('toman'))
                    ->sortable(false)
                    ->copyable()
                    ->numeric(),
            ])
            ->actions([])
            ->toggleableAll()
            ->modifyQueryUsing(fn(Builder $query) => $this->modifyTableQuery($query));
    }

    public function modifyTableQuery(Builder $query): Builder
    {
        $relation = [
            "productivities AS total_work_duration" => function (Builder $builder) {
                $builder->select(DB::raw('SEC_TO_TIME(SUM(TIME_TO_SEC(finished_at) - TIME_TO_SEC(started_at) - TIME_TO_SEC(leave_time)))'));
                $this->dayFilter($builder);
            }
        ];

        $query->with('users');
        $query->with([
            'productivities' => fn($builder) => $this->dayFilter($builder)
        ]);
        $query->withSum($relation, 'total_work_duration');

        return $query;
    }
}
