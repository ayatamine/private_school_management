<?php

namespace App\Filament\Resources\ParentModelResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ParentModelResource;

class EditParentModel extends EditRecord
{
    protected static string $resource = ParentModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
               
        $this->record->user?->update([
            'national_id' =>$data['national_id'],
            // 'gender' =>$data['gender'],
            'phone_number' =>$data['phone_number'],
            'email' =>$data['email'],
         ]);
         if($password =$data['password']) {
             $data['password'] = bcrypt($password);
             $this->record->user?->update([ 'password' => $data['password']]);
         }
        return $data;
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        // $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;
    
        return $data;
    }
}
