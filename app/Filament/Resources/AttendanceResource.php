<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name'),
                TextColumn::make('started_at')
                    ->jalaliDateTime(),
                TextColumn::make('finished_at')
                    ->jalaliDateTime(),
                TextColumn::make('reduce'),
                TextColumn::make('vacation'),
                TextColumn::make('home_work'),
                TextColumn::make('day')
                    ->jalaliDate(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }
}
