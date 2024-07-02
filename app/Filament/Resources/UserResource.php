<?php

namespace App\Filament\Resources;

use App\Filament\Auth\EditMyProfile;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends BaseResource
{

    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        $editProfile = new EditMyProfile();
        return $form
            ->schema([
                $editProfile->getFirstNameComponent(),
                $editProfile->getLastNameComponent(),
                $editProfile->getUsernameComponent(),
                $editProfile->getMobileComponent(),
                $editProfile->getEmailComponent(),
                $editProfile->getRoleComponent(),
                $editProfile->getPasswordComponent()
                    ->required(),
                $editProfile->getPasswordConfirmationComponent(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name'),
                TextColumn::make('username'),
                TextColumn::make('mobile'),
                TextColumn::make('email'),
                TextColumn::make('role')
                    ->formatStateUsing(fn(string $state): string => trans($state)),
                TextColumn::make('projects.title')->badge(),

            ])
            ->recordUrl(null)
            ->bulkActions([
                //
            ]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }
}
