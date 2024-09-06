<?php

namespace App\Filament\Resources;

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
    protected static ?int $navigationSort = 5;

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
                    ->time(),
                TextInput::make('exited_at')
                    ->after('entered_at')
                    ->time(),
                TextInput::make('reduce')
                    ->time(),
                TextInput::make('vacation')
                    ->time(),
                TextInput::make('home_work')
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
                TextInputColumn::make('vacation')->time(),
                TextInputColumn::make('home_work')->time(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }
}
