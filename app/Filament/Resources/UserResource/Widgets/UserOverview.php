<?php

namespace App\Filament\Resources\UserResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('name', '123')
            ->label('Name')
            ->color('primary')
            ->icon('heroicon-s-user')
            ->url('/123')
        ];
    }
}
