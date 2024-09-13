<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployerResource\Pages\ReportEmployer;
use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectResource extends BaseResource
{
    protected static ?string $model = Project::class;
    protected static string $navigationAfter = ReportEmployer::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
                TextColumn::make('amount'),

                TextColumn::make('fee')
                    ->prefix(trans('toman')),

                TextColumn::make('users.full_name')->badge(),
            ])
            ->toggleableAll()
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\RelationManagers\UsersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'report' => Pages\ReportProject::route('/report'),
        ];
    }
}
