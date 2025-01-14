<?php

namespace App\Filament\Resources\ParentModelResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ParentModelResource;

class ViewParent extends ViewRecord
{
    protected static string $resource = ParentModelResource::class;
    public function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        // $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;
        if($students =$this->record->students)
        {
            $stds=[];
            foreach($students as $i=>$student)
            {
                 $stds[$i]['username'] = $student->username;
                 $stds[$i]['national_id'] = $student->user?->national_id;
                 $stds[$i]['parent_relation'] = trans('main.'.$this->record->relation);
            }
          
        }
        $data['students'] = $stds;

        return $data;
    }
}
