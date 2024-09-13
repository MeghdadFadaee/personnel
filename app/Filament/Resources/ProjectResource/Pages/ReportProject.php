<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\ProjectResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;

class ReportProject extends BaseListRecords
{
    protected static string $resource = ProjectResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

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
        $columns = Arr::except($table->getColumns(), ['amount', 'users.full_name']);

        return $table
            ->columns([
                ...$columns,

                TextColumn::make('total_fee')
                    ->prefix(trans('toman'))
                    ->sortable(false)
                    ->copyable()
                    ->numeric(),

                TextColumn::make('performances_sum_completed_count')
                    ->numeric(),
            ])
            ->actions([])
            ->modifyQueryUsing(fn(Builder $query) => $query->withSum('performances', 'completed_count'));
    }
}
