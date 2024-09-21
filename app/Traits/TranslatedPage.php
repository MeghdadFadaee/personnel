<?php

namespace App\Traits;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use function Filament\Support\get_model_label;

trait TranslatedPage
{
    public static function getNavigationLabel(): string
    {
        return trans(parent::getNavigationLabel());
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel();
    }

    public function getModelLabel(): string
    {
        $modelLabel = get_model_label(static::getModel());
        return trans($modelLabel);
    }

    public function getPluralModelLabel(): string
    {
        $table = app(static::getModel())->getTable();
        return trans($table);
    }

    public function getBreadcrumb(): ?string
    {
        return Str::replace($this->getPluralModelLabel(), '', static::getNavigationLabel());
    }
}
