<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets;
use App\Traits\HasDayFilter;
use App\Traits\TranslatedPage;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;


class ReportDetailUser extends Page implements HasForms
{
    use InteractsWithRecord;
    use HasDayFilter;
    use TranslatedPage;

    protected static string $resource = UserResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string $view = 'filament.resources.reports.has-filter-form';

    public string $table = '';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected function getForms(): array
    {
        return [
            'filterForm',
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            Widgets\UserProductivities::make(),
            Widgets\UserPerformances::make(),
        ];
    }

    public function loadTable(): void
    {
        $this->dispatch('setUserProductivitiesFilters', $this->getResolvedParams());
        $this->dispatch('setUserPerformancesFilters', $this->getResolvedParams());
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->isAdmin();
    }
}
