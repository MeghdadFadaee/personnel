<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployerResource\Pages;
use App\Models\Employer;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Filament\Resources\UserResource\Pages\ReportUser;

class EmployerResource extends BaseResource
{
    protected static ?string $model = Employer::class;
    protected static string $navigationAfter = ReportUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title'),
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
            'index' => Pages\ListEmployers::route('/'),
            'create' => Pages\CreateEmployer::route('/create'),
            'edit' => Pages\EditEmployer::route('/{record}/edit'),
            'report' => Pages\ReportEmployer::route('/report'),
        ];
    }

    public static function getNavigationItems(): array
    {
        $selfItem = Arr::first(parent::getNavigationItems());

        return [
            $selfItem
                ->isActiveWhen(fn() => self::activeWhen()),
            ...Pages\ReportEmployer::getNavigationItems(),
        ];
    }

    public static function activeWhen(): bool
    {
        $requestRoute = Str::of(request()->route()->getName());
        $reportRoute = Pages\ReportEmployer::getRouteName();
        return $requestRoute->contains(self::getRouteBaseName()) and !$requestRoute->is($reportRoute) ;
    }
}
