<?php

namespace App\Filament\Resources\EmployerResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\EmployerResource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportEmployer extends BaseListRecords
{
    protected static string $resource = EmployerResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return parent::table(table: $table)
            ->actions([])
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('id', [1,2,3,4,5]));
    }
}
