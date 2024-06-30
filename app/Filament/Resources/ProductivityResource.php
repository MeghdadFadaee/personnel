<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ProductivityResource\Pages;
use App\Models\Productivity;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ProductivityResource extends BaseResource
{
    protected static ?string $model = Productivity::class;
    protected static ?int $navigationSort = 5;

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
                //
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
            'index' => Pages\ListProductivities::route('/'),
            'create' => Pages\CreateProductivity::route('/create'),
            'edit' => Pages\EditProductivity::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->isAdmin();
    }
}
