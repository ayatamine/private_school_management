<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Models\User;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        $user = User::create([
            'national_id' =>$data['national_id'],
            'gender' =>$data['gender'],
            'phone_number' =>$data['phone_number'],
            'email' =>$data['email'],
            'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
        ]);
        $data['registered_by'] = Auth::id();
        $data['user_id'] = $user?->id;
        $data['nationality'] = $data['nationality'] =="saudian" ? $data['nationality'] : $data['nationality2'];
        return $data;
    }
}
