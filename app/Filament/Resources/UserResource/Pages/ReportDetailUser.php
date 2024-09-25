<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Pages\BaseListRecords;
use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Traits\PageWithDayFilter;
use App\Traits\TranslatedPage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ReportDetailUser extends Page implements HasForms, HasTable
{
    use PageWithDayFilter;
    use TranslatedPage;
    use InteractsWithTable;

    protected static string $resource = UserResource::class;
    protected static string $view = 'filament.resources.reports.user-detail';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query());
    }

    public function reportUser()
    {
        return Table::make($this);
    }

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
