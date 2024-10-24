<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages\ReportProject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms\Form;
use Filament\Tables\Table;

class AttendanceResource extends BaseResource
{
    protected static ?string $model = Attendance::class;
    protected static string $navigationAfter = ReportProject::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user')
                    ->required()
                    ->setTitle('full_name'),

                DateTimePicker::make('day')
                    ->required()
                    ->jalali(),

                TextInput::make('entered_at')
                    ->default('00:00')
                    ->time(),
                TextInput::make('exited_at')
                    ->after('entered_at')
                    ->default('00:00')
                    ->time(),
                TextInput::make('reduce')
                    ->default('00:00')
                    ->time(),
                TextInput::make('vacation')
                    ->default('00:00')
                    ->time(),
                TextInput::make('home_work')
                    ->default('00:00')
                    ->time(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->sortable(['first_name', 'last_name']),

                TextColumn::make('day')
                    ->jalaliDate(),

                TextInputColumn::make('entered_at')->time(),
                TextInputColumn::make('exited_at')->time(),
                TextInputColumn::make('reduce')->time(),
                TextInputColumn::make('vacation')
                    ->visible(auth()->user()->isAdmin())
                    ->time(),
                TextInputColumn::make('home_work')->time(),
            ])
            ->filters([
                //
            ])
            ->toggleableAll()
            ->recordUrl(null);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        if (!auth()->user()->isAdmin()) {
            $query->forMe();
        }
        return $query;
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public static function can(string $action, ?Model $record = null): bool
    {
        return auth()->user()->isAdmin();
    }
}
