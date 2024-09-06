<?php

namespace App\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\ProductivityResource\Pages;
use App\Models\Productivity;
use Filament\Forms\Form;
use Filament\Tables\Table;

class ProductivityResource extends BaseResource
{
    protected static ?string $model = Productivity::class;
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user')
                    ->required()
                    ->setTitle('full_name'),

                Select::make('employer_id')
                    ->relationship('employer', 'title')
                    ->required(),

                DatePicker::make('day')
                    ->required()
                    ->jalali(),

                TextInput::make('description'),

                TextInput::make('started_at')
                    ->time(),

                TextInput::make('finished_at')
                    ->after('started_at')
                    ->time(),

                TextInput::make('leave_time')
                    ->time(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.full_name')
                    ->sortable(['first_name', 'last_name']),

                TextColumn::make('employer.title'),

                TextColumn::make('day')
                    ->jalaliDate(),

                TextInputColumn::make('started_at')->time(),
                TextInputColumn::make('finished_at')->time(),
                TextInputColumn::make('leave_time')->time(),
                TextInputColumn::make('description'),
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
            'index' => Pages\ListProductivities::route('/'),
            'create' => Pages\CreateProductivity::route('/create'),
            'edit' => Pages\EditProductivity::route('/{record}/edit'),
        ];
    }
}
