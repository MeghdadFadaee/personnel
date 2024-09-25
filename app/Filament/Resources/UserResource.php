<?php

namespace App\Filament\Resources;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Filament\Pages\Dashboard;
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
                                    ->default('00:00')
                                    ->time(),
                                TextInput::make('exited_at')
                                    ->default('00:00')
                                    ->after('entered_at')
                                    ->time(),
                                TextInput::make('daily_duty')
                                    ->default('00:00')
                                    ->time(),

                                TextInput::make('hourly_salary')
                                    ->suffix(trans('toman'))
                                    ->integer()
                                    ->nullable()
                                    ->default(0),

                                TextInput::make('hourly_penalty')
                                    ->suffix(trans('toman'))
                                    ->integer()
                                    ->nullable()
                                    ->default(0),
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
                    ->suffix(' '.trans('toman')),
                TextColumn::make('hourly_penalty')
                    ->suffix(' '.trans('toman')),
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
            'report' => Pages\ReportUser::route('/report'),
            'report.detail' => Pages\ReportDetailUser::route('/report/{record}'),
        ];
    }

    public static function getNavigationItems(): array
    {
        $selfItem = Arr::first(parent::getNavigationItems());
        $reportItem = Arr::first(Pages\ReportUser::getNavigationItems());

        return [
            $selfItem
                ->isActiveWhen(fn() => self::selfActiveWhen()),
            $reportItem
                ->isActiveWhen(fn() => self::reportActiveWhen()),
        ];
    }

    public static function selfActiveWhen(): bool
    {
        $requestRoute = Str::of(request()->route()->getName());
        $reportRoute = Pages\ReportUser::getRouteName();
        return $requestRoute->contains(self::getRouteBaseName()) and !$requestRoute->is($reportRoute.'*');
    }

    public static function reportActiveWhen(): bool
    {
        $requestRoute = Str::of(request()->route()->getName());
        $reportRoute = Pages\ReportUser::getRouteName();
        return $requestRoute->contains($reportRoute);
    }
}
