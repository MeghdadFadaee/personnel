<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Symfony\Component\Process\Process;

class UpdateRepository extends Widget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 2;
    protected static bool $isLazy = false;

    protected static string $view = 'filament.resources.widgets.update-repository';

    public string $reset = '';
    public string $pull = '';

    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function update(): void
    {
        $this->reset = shell_exec('git reset --hard');
        $this->pull = shell_exec('git pull');

        Notification::make()
            ->title(__('The action ran successfully!'))
            ->success()
            ->send();
    }
}
