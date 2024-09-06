<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use function Filament\Support\get_model_label;

abstract class BaseResource extends Resource
{
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

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }
}
