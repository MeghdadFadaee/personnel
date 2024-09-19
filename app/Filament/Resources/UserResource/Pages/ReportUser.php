<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\UserResource;
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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\Summarizers;
use Illuminate\Support\Number;

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

                $this->getDurationTextColumn('total_attendance_duration')

            ])
            ->actions([])
            ->toggleableAll()
            ->modifyQueryUsing(fn(Builder $query) => $this->modifyTableQuery($query));
    }

    public function modifyTableQuery(Builder $query): Builder
    {
        $query->whereKey(1);
        $query->withSum([
            "attendances AS total_attendance_duration" => function (Builder $builder) {
                $builder->select(DB::raw('SUM(TIME_TO_SEC(exited_at) - TIME_TO_SEC(entered_at))'));
                $this->dayFilter($builder);
            },
        ], 'total_attendance_duration');

        return $query;
    }
}
