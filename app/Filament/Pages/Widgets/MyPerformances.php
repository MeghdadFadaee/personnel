<?php

namespace App\Filament\Pages\Widgets;

use Closure;
use App\Models\Performance;
use Illuminate\Support\Arr;
use Illuminate\Validation\Validator;
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

    protected function getTableHeading(): string|Htmlable|null
    {
        return trans('My Performances');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->searchable(false)
            ->modelLabel(trans('performance'))
            ->pluralModelLabel(trans('performances'))
            ->columns([
                TextColumn::make('project.title'),
                $this->getCompletedCountComponent(),
            ])
            ->headerActions([
                $this->getHeaderAction(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->iconButton(),
            ]);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return Performance::query()->forMe()->forToday();

    }

    protected function getCompletedCountComponent(): TextInputColumn
    {
        return TextInputColumn::make('completed_count')
            ->rules([
                'integer',
                function (string $attribute, $value, Closure $fail, Validator $validator) {
                    $id = Arr::get(request()->request->all(), 'components.0.calls.0.params.1');
                    $performance = $this->getTableQuery()
                        ->find($id);

                    $ability = $performance->completed_count + $performance->project->amount - $performance->project->amount_done;
                    if ($ability < $value) {
                        $fail('validation.lte.numeric')->translate(['value' => $ability]);
                    }
                },
            ])
            ->summarize([
                Summarizers\Sum::make(),
            ]);
    }

    protected function getHeaderAction(): Tables\Actions\AttachAction
    {
        return Tables\Actions\AttachAction::make()
            ->form([
                Select::make('project')
                    ->options(auth()->user()->projects()->hasRemaining()->pluck('title', 'id'))
                    ->preload(),
            ])
            ->action(function (array $data) {
                Performance::create([
                    'user_id' => auth()->id(),
                    'project_id' => $data['project'],
                    'day' => today(),
                ]);
            });
    }
}
