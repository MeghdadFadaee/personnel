<?php

namespace App\Filament\Resources\ProductivityResource\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets;

class RegisterProductivity extends Page
{
    protected static ?int $navigationSort = 7;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static string $view = 'filament.resources.productivity-resource.pages.register-productivity';

    public static function getNavigationLabel(): string
    {
        return trans(parent::getNavigationLabel());
    }

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public static function canAccess(): bool
    {
        return true;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            Widgets\StatsOverviewWidget::make(),
            Widgets\MyProductivities::make(),
        ];
    }
}
