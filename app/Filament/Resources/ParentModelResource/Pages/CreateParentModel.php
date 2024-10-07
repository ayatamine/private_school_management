<?php

namespace App\Filament\Resources\ParentModelResource\Pages;

use Hash;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ParentModelResource;

class CreateParentModel extends CreateRecord
{
    protected static string $resource = ParentModelResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        $user = User::create([
            'national_id' =>$data['national_id'],
            // 'gender' =>$data['gender'],
            'phone_number' =>$data['phone_number'],
            'email' =>$data['email'],
            'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
        ]);
        $data['user_id'] = $user?->id;
    
        return $data;
    }
}
