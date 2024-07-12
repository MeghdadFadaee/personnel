<?php

namespace App\Filament\Pages\Widgets;

use App\Models\Productivity;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\Summarizers;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class MyProductivities extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->searchable(false)
            ->modelLabel(trans('productivity'))
            ->pluralModelLabel(trans('productivities'))
            ->columns([
                TextColumn::make('project.title'),
                TextInputColumn::make('started_at')
                    ->time()
                    ->rules([
                        'date_format:H:i',
                        function (string $attribute, $value, Closure $fail, Validator $validator) {
                            try {
                                $start = Carbon::parse($value);
                            } catch (\Carbon\Exceptions\InvalidFormatException $exception) {
                                return;
                            }

                            $id = Arr::get(request()->request->all(), 'components.0.calls.0.params.1');
                            $productivities = $this->getTableQuery()
                                ->where('id', '<>', $id)
                                ->get();

                            foreach ($productivities as $productivity) {
                                $finish = Carbon::parse($productivity->finished_at);
                                if ($start->isBefore($finish)) {
                                    $fail("validation.custom.started_at.after")->translate();
                                    return;
                                }
                            }
                        },
                    ]),
                TextInputColumn::make('finished_at')
                    ->time()
                    ->rules([
                        'date_format:H:i',
                        function (string $attribute, $value, Closure $fail, Validator $validator) {
                            try {
                                $finish = Carbon::parse($value);
                            } catch (\Carbon\Exceptions\InvalidFormatException $exception) {
                                return;
                            }

                            $id = Arr::get(request()->request->all(), 'components.0.calls.0.params.1');
                            $model = Productivity::query()->find($id);

                            $start = Carbon::parse($model->started_at);


                            if ($finish->isBefore($start)) {
                                $fail("validation.custom.finished_at.after")->translate();
                            }
                        },
                    ]),
                TextInputColumn::make('description')
                    ->summarize([
                        Summarizers\Sum::make()
                            ->formatStateUsing(function ($state) {
                                $starts = Carbon::parse('00:00:00');
                                $finishes = Carbon::parse('00:00:00');
                                $productivities = $this->getTableQuery()->get();
                                foreach ($productivities as $productivity) {
                                    if (!empty($productivity->started_at)) {
                                        $start = explode(':', $productivity->started_at);
                                        $starts->addHours((int) $start[0]);
                                        $starts->addMinutes((int) $start[1]);
                                    }
                                    if (!empty($productivity->finished_at)) {
                                        $finish = explode(':', $productivity->finished_at);
                                        $finishes->addHours((int) $finish[0]);
                                        $finishes->addMinutes((int) $finish[1]);
                                    }
                                }
                                return $finishes->diff($starts)->forHumans();
                            })
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
                        Productivity::create([
                            'user_id' => auth()->id(),
                            'project_id' => $data['project'],
                            'day' => today(),
                        ]);
                    })
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()->iconButton(),
            ]);
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        return Productivity::query()->forMe()->forToday();

    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return trans('My Productivities');
    }
}
