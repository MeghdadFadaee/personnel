<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\Concerns\CanSubmitForm;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

class RegisterActivity extends Page implements HasForms
{
    use InteractsWithForms, CanSubmitForm;

    protected static ?int $navigationSort = 8;

    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.resources.productivity-resource.pages.register-productivity';

    public User $user;
    public Attendance $attendance;
    public ?string $entered_at;
    public ?string $exited_at;
    public ?string $reduce;
    public ?string $vacation;
    public ?string $home_work;

    public static function getNavigationLabel(): string
    {
        return trans(parent::getNavigationLabel());
    }

    public function getTitle(): string
    {
        return self::getNavigationLabel();
    }

    public function getSubheading(): string
    {
        /* @var Carbon $day */
        $day = $this->attendance->day;
        return Jalalian::fromCarbon($day)->format('l: d-m-Y');
    }

    public function mount(): void
    {
        $this->user = auth()->user();
        $this->attendance = $this->user->attendances()->forToday()->first();

        $this->form->fill($this->attendance->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('entered_at')
                    ->time(),
                TextInput::make('exited_at')
                    ->after('entered_at')
                    ->time(),
                TextInput::make('reduce')
                    ->time(),
//                TextInput::make('vacation')
//                    ->time(),
                TextInput::make('home_work')
                    ->time(),

                Actions::make([
                    Actions\Action::make('save')
                        ->translateLabel()
                        ->action(fn() => $this->save()),
                ])
                ->verticallyAlignEnd(),
            ])
            ->model($this->attendance)
            ->columns(8);
    }

    public function save(): void
    {
        $this->validate();

        $this->attendance->update($this->form->getState());

        Notification::make()
            ->success()
            ->title(trans('filament-panels::resources/pages/edit-record.notifications.saved.title'))
            ->send();
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Pages\Widgets\MyProductivities::make(),
            \App\Filament\Pages\Widgets\MyPerformances::make(),
        ];
    }

    public function getFooterWidgetsColumns(): int|string|array
    {
        return 5;
    }
}
