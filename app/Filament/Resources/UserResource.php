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
        $isCreate = $form->getOperation() === "create";
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
                                TextInput::make('daily_duty')
                                    ->time(),

                                TextInput::make('hourly_salary')
                                    ->prefix(trans('toman'))
                                    ->integer()
                                    ->nullable()
                                    ->default(0),

//                                TextInput::make('piece_salary')
//                                    ->prefix(trans('toman'))
//                                    ->integer()
//                                    ->nullable()
//                                    ->default(0),
                            ]),
                        Tabs\Tab::make('Login information')
                            ->icon('heroicon-o-arrow-left-end-on-rectangle')
                            ->schema([
                                $editProfile->getUsernameComponent(),
                                $editProfile->getPasswordComponent()
                                    ->required($isCreate),
                                $editProfile->getPasswordConfirmationComponent(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->sortable(['first_name', 'last_name']),
                TextColumn::make('username'),
                TextColumn::make('mobile'),
                TextColumn::make('email'),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => trans($state)),
                TextColumn::make('entered_at'),
                TextColumn::make('exited_at'),
                TextColumn::make('daily_duty'),
                TextColumn::make('hourly_salary')
                    ->prefix(trans('toman')),
                TextColumn::make('piece_salary')
                    ->prefix(trans('toman')),
                TextColumn::make('employers.title')->badge(),
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
}
