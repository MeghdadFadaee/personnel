<?php

namespace App\Filament\Auth;

use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile;
use Filament\Forms\Components\ToggleButtons;

class EditMyProfile extends EditProfile
{
    /**
     * @throws \Exception
     */
    public function form(Form $form): Form
    {
        $form = parent::form($form);
        $form->columns(1);
        return $form
            ->schema([
                $this->getFirstNameComponent(),
                $this->getLastNameComponent(),
                $this->getUsernameComponent(),
                $this->getMobileComponent(),
                $this->getEmailComponent(),
                $this->getPasswordComponent(),
                $this->getPasswordConfirmationComponent(),
            ]);
    }

    public function getPasswordComponent(): TextInput|Component
    {
        return $this->getPasswordFormComponent();
    }

    public function getPasswordConfirmationComponent(): TextInput|Component
    {
        return $this->getPasswordConfirmationFormComponent();
    }

    public function getEmailComponent(): TextInput
    {
        return $this->getEmailFormComponent()->required(false);
    }

    public function getUsernameComponent(): TextInput
    {
        if (auth()->user()->isAdmin()) {
            return TextInput::make('username')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255);
        }
        return TextInput::make('username')
            ->disabled();
    }

    public function getFirstNameComponent(): TextInput
    {
        return TextInput::make('first_name')
            ->maxLength(255);
    }

    public function getLastNameComponent(): TextInput
    {
        return TextInput::make('last_name')
            ->maxLength(255);
    }

    public function getMobileComponent(): TextInput
    {
        return TextInput::make('mobile')
            ->unique(ignoreRecord: true)
            ->maxLength(255);
    }

    public function getRoleComponent(): ToggleButtons
    {
        return ToggleButtons::make('role')
            ->options([
                User::ROLE_ADMIN => trans(User::ROLE_ADMIN),
                User::ROLE_USER => trans(User::ROLE_USER),
            ])
            ->inline()
            ->default(User::ROLE_USER);
    }
}
