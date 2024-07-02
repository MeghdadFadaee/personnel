<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ProductivityResource\Pages;
use App\Models\Productivity;
use Filament\Forms\Form;
use Filament\Tables\Table;

class ProductivityResource extends BaseResource
{
    protected static ?string $model = Productivity::class;
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user')
                    ->required()
                    ->setTitle('full_name'),
                Select::make('project_id')
                    ->relationship('project', 'title')
                    ->required(),

                DatePicker::make('day')
                    ->required()
                    ->jalali(),

                TextInput::make('Description'),
                TextInput::make('started_at')
                    ->time(),
                TextInput::make('finished_at')
                    ->after('started_at')
                    ->time(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name'),
                TextColumn::make('project.title'),
                TextColumn::make('day')
                    ->jalaliDate(),
                TextInputColumn::make('started_at')->time(),
                TextInputColumn::make('finished_at')->time(),
                TextInputColumn::make('description'),
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
