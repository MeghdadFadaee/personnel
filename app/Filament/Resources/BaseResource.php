<?php

namespace App\Filament\Resources;

use Filament\Pages\Dashboard;
use Filament\Resources\Resource;
use function Filament\Support\get_model_label;

abstract class BaseResource extends Resource
{
    protected static string $navigationAfter = Dashboard::class;

    public static function getModelLabel(): string
    {
        $modelLabel = get_model_label(static::getModel());
        return trans($modelLabel);
    }

    public static function getPluralModelLabel(): string
    {
        $table = app(static::getModel())->getTable();
        return trans($table);
    }

    public static function getNavigationSort(): ?int
    {
        return static::$navigationAfter::getNavigationSort() + 1;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }
}
