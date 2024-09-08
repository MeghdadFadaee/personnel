<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\PerformanceResource\Pages;
use App\Models\Performance;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PerformanceResource extends BaseResource
{
    protected static ?string $model = Performance::class;
    protected static ?int $navigationSort = 7;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user')
                    ->required()
                    ->setTitle('full_name'),

                Select::make('project_id')
                    ->relationship('project', 'title')
                    ->required(),

                DatePicker::make('day')
                    ->required()
                    ->jalali(),

                TextInput::make('completed_count')
                    ->default(0)
                    ->integer(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->sortable(['first_name', 'last_name']),

                TextColumn::make('project.title'),

                TextColumn::make('day')
                    ->jalaliDate(),

                TextColumn::make('completed_count'),

            ])
            ->toggleableAll()
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPerformances::route('/'),
            'create' => Pages\CreatePerformance::route('/create'),
            'edit' => Pages\EditPerformance::route('/{record}/edit'),
        ];
    }
}
