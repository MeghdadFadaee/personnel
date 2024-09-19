<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\UserResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ReportUser extends BaseListRecords implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = UserResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
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
                TextInput::make('full_name')
                    ->required()
                    ->columnSpan(1)
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        $table = parent::table(table: $table);

        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->sortable(['first_name', 'last_name']),

                $this->getDurationTextColumn('total_attendance_duration'),
                $this->getDurationTextColumn('total_delay_duration'),
                $this->getDurationTextColumn('total_worked_duration'),
                $this->getDurationTextColumn('total_worked_all_duration'),
                $this->getDurationTextColumn('total_vacation_duration'),
                $this->getDurationTextColumn('total_reduce_daily_duty_duration'),

            ])
            ->actions([])
            ->toggleableAll()
            ->modifyQueryUsing(fn(Builder $query) => $this->modifyTableQuery($query));
    }

    public function modifyTableQuery(Builder $query): Builder
    {
        $query->whereKey(1);
        $this->withAttendancesSum(
            $query,
            'total_attendance_duration',
            'TIME_TO_SEC(exited_at) - TIME_TO_SEC(entered_at)',
        );

        $this->withAttendancesSum(
            $query,
            'total_delay_duration',
            'TIME_TO_SEC(entered_at) - TIME_TO_SEC(users.entered_at)',
        );

        $this->withAttendancesSum(
            $query,
            'total_worked_duration',
            'TIME_TO_SEC(exited_at) - TIME_TO_SEC(entered_at) - TIME_TO_SEC(reduce)',
        );

        $this->withAttendancesSum(
            $query,
            'total_worked_all_duration',
            'TIME_TO_SEC(exited_at) - TIME_TO_SEC(entered_at) - TIME_TO_SEC(reduce) - TIME_TO_SEC(vacation) + TIME_TO_SEC(home_work)',
        );

        $this->withAttendancesSum(
            $query,
            'total_vacation_duration',
            'TIME_TO_SEC(vacation)',
        );

        $this->withAttendancesSum(
            $query,
            'total_reduce_daily_duty_duration',
            'TIME_TO_SEC(users.daily_duty) - (TIME_TO_SEC(exited_at) - TIME_TO_SEC(entered_at) + TIME_TO_SEC(home_work)) - TIME_TO_SEC(reduce) + TIME_TO_SEC(vacation)',
        );

        return $query;
    }

    public function withAttendancesSum(Builder &$query, string $AS, string $row): void
    {
        $query->withSum([
            "attendances AS $AS" => function (Builder $builder) use ($row){
                $builder->select(DB::raw("SUM($row)"));
                $this->dayFilter($builder);
            },
        ], $AS);
    }
}
