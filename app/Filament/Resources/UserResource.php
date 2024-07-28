<?php

namespace App\Filament\Resources;

use App\Filament\Auth\EditMyProfile;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
class UserResource extends BaseResource
{

    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        $editProfile = new EditMyProfile();
        return $form
            ->columns(4)
            ->schema([
                Placeholder::make(''),
                Tabs::make('Tabs')
                    ->columnSpan(2)
                    ->tabs([
                        Tabs\Tab::make('Profile')
                            ->icon('heroicon-o-user')
                            ->schema([
                                $editProfile->getFirstNameComponent(),
                                $editProfile->getLastNameComponent(),
                                $editProfile->getUsernameComponent(),
                                $editProfile->getMobileComponent(),
                                $editProfile->getEmailComponent(),
                                $editProfile->getRoleComponent()
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Work Hours')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                TextInput::make('entered_at')
                                    ->time(),
                                TextInput::make('exited_at')
                                    ->after('entered_at')
                                    ->time(),

                                TextInput::make('wage')
                                    ->prefix(trans('toman'))
                                    ->integer()
                                    ->nullable()
                                    ->maxLength(255),

                                TextInput::make('daily_duty')
                                    ->time(),
                            ]),
                        Tabs\Tab::make('Password')
                            ->icon('heroicon-o-lock-closed')
                            ->schema([
                                $editProfile->getPasswordComponent(),
                                $editProfile->getPasswordConfirmationComponent(),
                            ]),
                    ]),
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
                TextColumn::make('entered_at'),
                TextColumn::make('exited_at'),
                TextColumn::make('wage')
                    ->prefix(trans('toman')),
                TextColumn::make('daily_duty'),
                TextColumn::make('projects.title')->badge(),
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
