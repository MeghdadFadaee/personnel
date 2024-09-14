<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BaseResource;
use Carbon\Carbon;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use function Filament\Support\get_model_label;

/**
 * @property BaseResource $resource
 */
abstract class BaseListRecords extends ListRecords
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

    public static function getNavigationSort(): ?int
    {
        return static::$resource::getNavigationSort() + 1;
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->isAdmin();
    }

    public function getBreadcrumb(): ?string
    {
        return Str::replace($this->getPluralModelLabel(), '', static::getNavigationLabel());
    }

    public function dayFilter(Builder|HasMany &$query): Builder|HasMany
    {
        if (isset($this->starts_at) and Carbon::canBeCreatedFromFormat($this->starts_at, 'Y-m-d H:i:s')) {
            $query->whereDate('day', '>=', $this->starts_at);
        }
        if (isset($this->ends_at) and Carbon::canBeCreatedFromFormat($this->ends_at, 'Y-m-d H:i:s')) {
            $query->whereDate('day', '<=', $this->ends_at);
        }
        return $query;
    }

}
