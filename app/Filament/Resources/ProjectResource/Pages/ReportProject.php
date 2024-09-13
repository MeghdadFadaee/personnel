<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\ProjectResource;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;

class ReportProject extends BaseListRecords implements HasForms
{
    use InteractsWithForms;

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

    public string $starts_at;
    public string $ends_at;

    public function filterForm(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('starts_at')
                    ->afterStateUpdated(fn(?string $state) => $this->reloadTable())
                    ->columnSpan(1)
                    ->jalali()
                    ->live(),

                DatePicker::make('ends_at')
                    ->afterStateUpdated(fn(?string $state) => $this->reloadTable())
                    ->columnSpan(1)
                    ->jalali()
                    ->live(),

            ])
            ->columns(4);
    }

    public function reloadTable(): void
    {

        $this->table->modifyQueryUsing(function (Builder $query) {

            $relationFilter = function (Builder $builder) {
                if (isset($this->starts_at) and Carbon::canBeCreatedFromFormat($this->starts_at, 'Y-m-d H:i:s')) {
                    $builder->whereDate('day', '>=', $this->starts_at);
                }

                if (isset($this->ends_at) and Carbon::canBeCreatedFromFormat($this->ends_at, 'Y-m-d H:i:s')) {
                    $builder->whereDate('day', '<=', $this->ends_at);
                }
            };

            return self::getResource()::getEloquentQuery()
                ->withSum(['performances' => $relationFilter], 'completed_count');
        });

        $this->loadTable();
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
                    ->prefix(trans('toman'))
                    ->integer()
                    ->nullable()
                    ->default(0),

                TextInput::make('total_fee')
                    ->prefix(trans('toman'))
                    ->readOnly()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        $table = parent::table(table: $table);
        $columns = Arr::except($table->getColumns(), ['users.full_name']);

        return $table
            ->columns([
                ...$columns,

                TextColumn::make('total_fee')
                    ->summarize([
                        Summarizers\Summarizer::make()
                            ->label(trans('Sum'))
                            ->formatStateUsing(fn($state) => $this->getTotalFeeSum())
                    ])
                    ->prefix(trans('toman'))
                    ->sortable(false)
                    ->copyable()
                    ->numeric(),

                TextColumn::make('performances_sum_completed_count')
                    ->numeric(),
            ])
            ->actions([])
            ->toggleableAll()
            ->modifyQueryUsing(fn(Builder $query) => $query->withSum('performances', 'completed_count'));
    }

    public function getTotalFeeSum(): string
    {
        $projects = $this->table->getQuery()->get();
        return Number::format($projects->sum('total_fee'), locale: config('app.locale'));
    }}
