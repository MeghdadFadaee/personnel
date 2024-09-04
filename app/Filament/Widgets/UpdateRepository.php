<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Symfony\Component\Process\Process;

class UpdateRepository extends Widget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 1;
    protected static bool $isLazy = false;

    protected static string $view = 'filament.resources.widgets.update-repository';

    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function update(): void
    {
        $command = new Process(["git reset --hard", "git pull"]);
        $command->setWorkingDirectory(base_path());
        $command->run();

        if($command->isSuccessful()){
            Notification::make()
                ->title(__('The action ran successfully!'))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title(__('The action was executed successfully.'))
                ->danger()
                ->send();
        }
    }
}
