<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\UserResource;
use App\Traits\PageWithDayFilter;
use App\Traits\TranslatedPage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ReportDetailUser extends Page implements HasForms
{
    use PageWithDayFilter;
    use TranslatedPage;

    protected static string $resource = UserResource::class;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static string $view = 'filament.resources.reports.has-filter-form';

    public string $table = '';

    protected function getForms(): array
    {
        return [
            'filterForm',
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->isAdmin();
    }
}
