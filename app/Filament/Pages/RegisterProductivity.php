<?php

namespace App\Filament\Pages;

use App\Models\Attendance;
use App\Models\User;
use Ariaieboy\Jalali\CalendarUtils;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanSubmitForm;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class RegisterProductivity extends Page implements HasForms
{
    use InteractsWithForms, CanSubmitForm;

    protected static ?int $navigationSort = 7;
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.resources.productivity-resource.pages.register-productivity';

    public User $user;
    public Attendance $attendance;
    public ?string $started_at;
    public ?string $finished_at;
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
        $jalali = CalendarUtils::toJalali($day->year, $day->month, $day->day);
        return $day->dayName.': '.Arr::join($jalali, '-');
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
                TextInput::make('started_at')
                    ->time(),
                TextInput::make('finished_at')
                    ->after('started_at')
                    ->time(),
                TextInput::make('reduce')
                    ->time(),
                TextInput::make('vacation')
                    ->time(),
                TextInput::make('home_work')
                    ->time(),
            ])
            ->model($this->attendance)
            ->columns(5);
    }

    public function save(): void
    {
        $this->validate();

        $this->attendance->update($this->form->getState());
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->translateLabel()
                ->action(fn () => $this->save())
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Pages\Widgets\MyProductivities::make(),
        ];
    }
}