<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms\Form;
use Filament\Tables\Table;

class AttendanceResource extends BaseResource
{
    protected static ?string $model = Attendance::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user')
                    ->required()
                    ->setTitle('full_name'),

                DatePicker::make('day')
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
        $user = auth()->user();
        $columns = [
            TextColumn::make('user.full_name'),
            TextColumn::make('day')
                ->jalaliDate(),
        ];
        if ($user->isAdmin()) {
            $columns[] = TextInputColumn::make('entered_at')->time();
            $columns[] = TextInputColumn::make('exited_at')->time();
            $columns[] = TextInputColumn::make('reduce')->time();
            $columns[] = TextInputColumn::make('vacation')->time();
            $columns[] = TextInputColumn::make('home_work')->time();
        } else {
            $columns[] = TextColumn::make('entered_at');
            $columns[] = TextColumn::make('exited_atv');
            $columns[] = TextColumn::make('reduce');
            $columns[] = TextColumn::make('vacation');
            $columns[] = TextColumn::make('home_work');
        }

        return $table
            ->columns($columns)
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

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        return match (true) {
            $user->isAdmin() => parent::getEloquentQuery(),
            default => parent::getEloquentQuery()->mine()
        };
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }

//    public static function canCreate(): bool
//    {
//        return auth()->user()->isAdmin();
//    }
//
//    public static function canEdit(Model $record): bool
//    {
//        return auth()->user()->isAdmin();
//    }
//
//    public static function canDelete(Model $record): bool
//    {
//        return auth()->user()->isAdmin();
//    }
//
//    public static function canDeleteAny(): bool
//    {
//        return auth()->user()->isAdmin();
//    }

}
