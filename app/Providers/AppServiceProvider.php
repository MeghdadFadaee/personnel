<?php

namespace App\Providers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Field;
use Illuminate\Database\Query\Builder;
use Filament\Infolists\Components\Entry;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Filament\Tables\Columns\TextInputColumn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        Table::configureUsing(function (Table $table): void {
            $table->recordUrl('');
            $table->selectable();
            $table->actions([
                Tables\Actions\ViewAction::make()->iconButton(),
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ]);

        });
        Column::configureUsing(function (Column $column): void {
            $column->translateLabel();
            $column->searchable();
            $column->sortable();
        });
        Select::configureUsing(function (Select $column): void {
            $column->translateLabel();
            $column->searchable();
            $column->preload();
        });
        Filter::configureUsing(function (Filter $filter): void {
            $filter->translateLabel();
        });
        Field::configureUsing(function (Field $field): void {
            $field->translateLabel();
        });
        Entry::configureUsing(function (Entry $entry): void {
            $entry->translateLabel();
        });

        Builder::macro('mine', function (string $column = 'user_id') {
            /* @var Builder $this */
            return $this->where($column, auth()->id());
        });
        Select::macro('setTitle', function (string $attribute) {
            /* @var Select $this */
            return $this->getOptionLabelFromRecordUsing(fn($record) => (string) $record[$attribute]);
        });
        TextInput::macro('time', function () {
            /* @var TextInput $this */
            $this->mask('99:99');
            $this->placeholder('__:__');
            $this->rules(['date_format:H:i']);
            return $this;
        });
        TextInputColumn::macro('time', function () {
            /* @var TextInputColumn $this */
            $this->mask('99:99');
            $this->placeholder('__:__');
            $this->rules(['date_format:H:i']);
            $this->getState();
            return $this;
        });
        TextInputColumn::macro('after', function ($field) {
            /* @var TextInputColumn $this */
            $this->rules[] = 'after:'.$field;
            return $this;
        });
    }
}
