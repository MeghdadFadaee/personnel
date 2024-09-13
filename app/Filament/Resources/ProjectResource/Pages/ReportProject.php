<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\EmployerResource;
use App\Filament\Resources\ProjectResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                    ->maxLength(255),

                TextInput::make('amount')
                    ->integer()
                    ->default(0),

                TextInput::make('fee')
                    ->prefix(trans('toman'))
                    ->integer()
                    ->nullable()
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return parent::table(table: $table)
            ->actions([])
            ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('id', [1, 2, 3, 4, 5]));
    }
}
