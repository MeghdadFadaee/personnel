<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployerResource\Pages\ReportEmployer;
use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProjectResource extends BaseResource
{
    protected static ?string $model = Project::class;
    protected static string $navigationAfter = ReportEmployer::class;

    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-ripple';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('employer_id')
                    ->relationship('employer', 'title')
                    ->required(),

                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                TextInput::make('amount')
                    ->integer()
                    ->default(0),

                TextInput::make('fee')
                    ->suffix(trans('toman'))
                    ->integer()
                    ->nullable()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('employer.title')
                    ->label(trans('employer'))
                    ->collapsible(),
            ])
            ->groupingSettingsInDropdownOnDesktop()
            ->defaultGroup('employer.title')
            ->columns([
                TextColumn::make('employer.title'),
                TextColumn::make('title'),
                TextColumn::make('amount')
                    ->numeric(),

                TextColumn::make('fee')
                    ->suffix(' '.trans('toman'))
                    ->numeric(),

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

    public static function getNavigationItems(): array
    {
        $selfItem = Arr::first(parent::getNavigationItems());

        return [
            $selfItem
                ->isActiveWhen(fn() => self::activeWhen()),
            ...Pages\ReportProject::getNavigationItems(),
        ];
    }

    public static function activeWhen(): bool
    {
        $requestRoute = Str::of(request()->route()->getName());
        $reportRoute = Pages\ReportProject::getRouteName();
        return $requestRoute->contains(self::getRouteBaseName()) and !$requestRoute->is($reportRoute);
    }
}
