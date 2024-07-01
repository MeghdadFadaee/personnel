<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TimePicker;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceResource extends BaseResource
{
    protected static ?string $model = Attendance::class;
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TimePicker::make('started_at'),
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
            $columns[] = TextInputColumn::make('started_at')->time();
            $columns[] = TextInputColumn::make('finished_at')->time();
            $columns[] = TextInputColumn::make('reduce')->time();
            $columns[] = TextInputColumn::make('vacation')->time();
            $columns[] = TextInputColumn::make('home_work')->time();
        } else {
            $columns[] = TextColumn::make('started_at');
            $columns[] = TextColumn::make('finished_at');
            $columns[] = TextColumn::make('reduce');
            $columns[] = TextColumn::make('vacation');
            $columns[] = TextColumn::make('home_work');
        }

        return $table
            ->columns($columns)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->isAdmin();
    }

}
