<?php

namespace App\Filament\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Component;
use Filament\Pages\Auth\Login as BaseAuth;
 
class Login extends BaseAuth
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNationalIdFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }
 
    protected function getNationalIdFormComponent(): Component 
    {
        return TextInput::make('national_id')
            ->label('رقم الهوية')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    } 

    protected function getCredentialsFromFormData(array $data): array
    {
 
        return [
            'national_id' => $data['national_id'],
            'password'  => $data['password'],
        ];
    }
}