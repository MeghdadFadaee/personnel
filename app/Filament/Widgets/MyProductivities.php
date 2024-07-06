<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Productivity;
use Filament\Widgets\TableWidget;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Tables\Columns\TextInputColumn;
use App\Filament\Resources\AttendanceResource;
use Illuminate\Database\Eloquent\Relations\Relation;

class MyProductivities extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->searchable()
            ->columns([
                TextColumn::make('project.title'),
                TextColumn::make('day')
                    ->jalaliDate(),
                TextInputColumn::make('started_at')->time(),
                TextInputColumn::make('finished_at')->time(),
                TextInputColumn::make('description'),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->url(fn ($record): string => AttendanceResource::getUrl('edit', ['record' => $record])),
            ]);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return Productivity::query();

    }
    protected function getTableHeading(): string|Htmlable|null
    {
        return trans('My Productivities');
    }
}
