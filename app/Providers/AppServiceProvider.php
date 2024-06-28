<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Resources\Resource;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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

        Column::configureUsing(function(Column $column): void {
            $column->translateLabel();
            $column->searchable();
            $column->sortable();
        });
        Filter::configureUsing(function(Filter $filter): void {
            $filter->translateLabel();
        });
        Field::configureUsing(function(Field $field): void {
            $field->translateLabel();
        });
        Entry::configureUsing(function(Entry $entry): void {
            $entry->translateLabel();
        });
    }
}
