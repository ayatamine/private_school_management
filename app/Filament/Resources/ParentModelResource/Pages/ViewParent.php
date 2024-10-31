<?php

namespace App\Filament\Resources\ParentModelResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ParentModelResource;

class ViewParent extends ViewRecord
{
    protected static string $resource = ParentModelResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        // $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;
        if($student =$this->record->student)
        {
            $data['username'] = $student->username;
            $data['national_id'] = $student->user->national_id;
            $data['course'] = $student->semester->course->name;
        }
        return $data;
    }
}
