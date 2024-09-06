<?php

namespace App\Filament\Pages\Widgets;

use App\Models\Performance;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\Summarizers;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class MyPerformances extends TableWidget
{
    protected int|string|array $columnSpan = 2;

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->searchable(false)
            ->modelLabel(trans('performance'))
            ->pluralModelLabel(trans('performances'))
            ->columns([
                TextColumn::make('project.title'),
                TextInputColumn::make('completed_count')
                    ->rules(['integer'])
                    ->summarize([
                        Summarizers\Sum::make()
                    ])
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form([
                        Select::make('project')
                            ->options(auth()->user()->projects()->pluck('title', 'id'))
                            ->preload()
                    ])
                    ->action(function (array $data) {
                        Performance::create([
                            'user_id' => auth()->id(),
                            'project_id' => $data['project'],
                        ]);
                    })
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->iconButton(),
            ]);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return Performance::query()->forMe()->forToday();

    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return trans('My Performances');
    }
}
