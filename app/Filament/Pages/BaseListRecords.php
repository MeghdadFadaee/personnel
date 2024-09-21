<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BaseResource;
use App\Traits\TranslatedPage;
use Filament\Resources\Pages\ListRecords;

/**
 * @property BaseResource $resource
 */
abstract class BaseListRecords extends ListRecords
{
    use TranslatedPage;

    public static function getNavigationSort(): ?int
    {
        return static::$resource::getNavigationSort() + 1;
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->isAdmin();
    }
}
