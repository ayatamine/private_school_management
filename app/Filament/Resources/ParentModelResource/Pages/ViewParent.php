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
        if($students =$this->record->students)
        {
            // $data['username'] = $student->username;
            // $data['national_id'] = $student->user->national_id;
            // $data['course'] = $student->semester->course->name;
            $stds=[];
            foreach($students as $i=>$student)
            {
                 $stds[$i]['username'] = $student->username;
                 $stds[$i]['national_id'] = $student->user?->national_id;
                 $stds[$i]['course'] = $student->semester?->course?->name;
            }
          
        }
        $data['students'] = $stds;

        return $data;
    }
}
