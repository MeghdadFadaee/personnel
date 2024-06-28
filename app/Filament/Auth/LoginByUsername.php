<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;

class LoginByUsername extends Login
{
    protected string $loginBy = 'username';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    private function getUsernameFormComponent(): TextInput
    {
        return TextInput::make($this->loginBy)
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {

        return [
            $this->loginBy => $data[$this->loginBy],
            'password' => $data['password'],
        ];
    }

    /**
     * @throws ValidationException
     */
    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.'.$this->loginBy => trans('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
