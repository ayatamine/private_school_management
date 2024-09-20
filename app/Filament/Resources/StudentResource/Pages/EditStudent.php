<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Course;
use App\Models\ParentModel;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\StudentResource;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;

        if( $data['course_id'])
        {
            $data['academic_year_id'] = Course::find($data['course_id'])->academic_year_id;

        }
       
        
        $parent = ParentModel::find($data['parent_id']);
        $data['parent_relation']  = $parent?->relation ? trans("main.".$parent?->relation."") : "";
        $data['parent_national_id']  = $parent?->user->national_id;
        $data['parent_email']  = $parent?->user->email;
        $data['parent_phone_number']  = $parent?->user->phone_number;
        $data['parent_gender']  = $parent?->user->gender;

        return $data;
    }
}