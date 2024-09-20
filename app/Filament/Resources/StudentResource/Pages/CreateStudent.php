<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        if($data['new_student'] == true)
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
        else
        {
           
            $Student = Student::findOrFail(intval($data['registration_number']));
            $Student->update([
                "created_at" =>$data['created_at'],
                "academic_year_id" =>$data['academic_year_id'],
                "course_id" =>$data['course_id'],
                "parent_id" =>$data['parent_id'],
                "opening_balance" =>$data['opening_balance'],
                "finance_document" =>$data['finance_document'],
                "note" =>$data['note'],
            ]);
            $data = $Student->toArray();
            Notification::make()
                        ->title(trans('main.student_registered_successfully'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();
            return [];
        }
    }
}