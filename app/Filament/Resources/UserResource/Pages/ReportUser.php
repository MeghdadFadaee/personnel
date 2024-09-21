<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\UserResource;
use App\Traits\PageWithDayFilter;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers;
use Illuminate\Support\Number;

class ReportUser extends BaseListRecords implements HasForms
{
    use InteractsWithForms;
    use PageWithDayFilter;

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

                $this->getDurationTextColumn('total_attendance_duration', 'حضور', 'success'),
                $this->getDurationTextColumn('total_delay_duration', 'تاخیر', 'info'),
                $this->getDurationTextColumn('total_worked_duration', 'جمع کار کرد', 'success'),
                $this->getDurationTextColumn('total_worked_all_duration', 'جمع کار کرد کل', 'success'),
                $this->getDurationTextColumn('total_reduce_duration', 'کسر روزانه', 'info'),
                $this->getDurationTextColumn('total_reduce_daily_duty_duration', 'کسر موظفی', 'danger'),
                $this->getDurationTextColumn('total_home_work_duration', 'کار در منزل', 'info'),
                $this->getDurationTextColumn('total_overtime_duration', 'اضافه کاری', 'info'),
                $this->getDurationTextColumn('total_vacation_duration', 'مرخصی', 'danger'),

                $this->getTomanTextColumn('total_hourly_salaries', 'حق الزحمه ساعتی', 'success'),
                $this->getTomanTextColumn('total_project_salaries', 'حق الزحمه پروژه‌ای', 'success'),
                $this->getTomanTextColumn('total_hourly_penalties', 'جریمه', 'danger'),
                $this->getTomanTextColumn('total_salaries', 'جمع کارکرد', 'success'),
            ])
            ->actions([])
            ->toggleableAll()
            ->recordUrl(fn($record) => route('filament.admin.resources.users.report.detail', $record), true)
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
            'total_reduce_duration',
            'TIME_TO_SEC(reduce)',
        );

        $this->withAttendancesSum(
            $query,
            'total_reduce_daily_duty_duration',
            'TIME_TO_SEC(users.daily_duty) - (TIME_TO_SEC(exited_at) - TIME_TO_SEC(entered_at) + TIME_TO_SEC(home_work)) - TIME_TO_SEC(reduce) + TIME_TO_SEC(vacation)',
        );

        $this->withAttendancesSum(
            $query,
            'total_home_work_duration',
            'TIME_TO_SEC(home_work)',
        );

        $this->withAttendancesSum(
            $query,
            'total_overtime_duration',
            '(TIME_TO_SEC(exited_at) - TIME_TO_SEC(entered_at) - TIME_TO_SEC(reduce) - TIME_TO_SEC(vacation) + TIME_TO_SEC(home_work)) - TIME_TO_SEC(users.daily_duty)',
        );

        $this->withAttendancesSum(
            $query,
            'total_vacation_duration',
            'TIME_TO_SEC(vacation)',
        );

        return $query;
    }

    public function withAttendancesSum(Builder &$query, string $AS, string $row): void
    {
        $query->withSum([
            "attendances AS $AS" => function (Builder $builder) use ($row) {
                $builder->select(DB::raw("SUM($row)"));
                $this->dayFilter($builder);
            },
        ], $AS);
    }

    public function getDurationTextColumn(string $name, ?string $label = null, ?string $color = null): TextColumn
    {
        return TextColumn::make($name)
            ->label(empty($label) ? $name : $label)
            ->color($color)
            ->copyable()
            ->formatStateUsing(fn($state) => match (true) {
                $state > 0 => Carbon::createFromTime()->addSeconds((int) $state)->format('H:i:s'),
                $state < 0 => Carbon::createFromTime()->subSeconds((int) $state)->format('H:i:s'),
                default => null,
            })
            ->tooltip(fn($state) => Carbon::createFromTime()
                ->addSeconds((int) $state)
                ->diff('00:00:00')
                ->forHumans()
            )
            ->summarize([
                Summarizers\Summarizer::make()
                    ->label(trans('Sum'))
                    ->formatStateUsing(fn($state) => Carbon::createFromTime()
                        ->addSeconds(
                            $this->table
                                ->getQuery()
                                ->get()
                                ->sum($name)
                        )
                        ->diff('00:00:00')
                        ->forHumans())
            ])
            ->copyable();
    }

    public function getTomanTextColumn(string $name, ?string $label = null, ?string $color = null): TextColumn
    {
        return TextColumn::make($name)
            ->label(empty($label) ? $name : $label)
            ->color($color)
            ->suffix(' '.trans('toman'))
            ->sortable(false)
            ->copyable()
            ->summarize([
                Summarizers\Summarizer::make()
                    ->label(trans('Sum'))
                    ->formatStateUsing(fn($state) => Number::format(
                            $this->table
                                ->getQuery()
                                ->get()
                                ->sum($name),
                            locale: config('app.locale')
                        ).' تومان'
                    ),
            ])
            ->numeric();
    }
}
