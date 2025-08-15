<?php

namespace App\Filament\Resources\NewestStudentResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Student;
use App\Models\Semester;
use App\Models\ParentModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\NewestStudentResource;

class EditNewestStudent extends EditRecord
{
    protected static string $resource = NewestStudentResource::class;

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

        $data['birth_date'] = date('Y-m-d' ,strtotime($data['birth_date']));
        //get last registration semester
        $data['semester_id'] = $this->record->semesters()->orderBy('id', 'desc')->first()->semester_id;
       
        if( $data['semester_id'] && $data['semester_id'] != "")
        {
            $data['academic_year_id'] = Semester::find($data['semester_id'])->academic_year_id;
            $data['academic_stage_id'] = Semester::find($data['semester_id'])->course->academic_stage_id;
            $data['course_id'] = Semester::find($data['semester_id'])->course_id;

        }

        
        $parent = ParentModel::find($data['parent_id']);
        $data['parent_relation']  = $parent?->relation ;
        $data['parent_national_id']  = $parent?->user->national_id;
        $data['parent_email']  = $parent?->user->email;
        $data['parent_phone_number']  = $parent?->user->phone_number;
        $data['parent_gender']  =$data['parent_gender']  = $parent?->user->gender ? trans("main.".$parent?->user?->gender."") : "";

        if($data['nationality'] == 'saudian')
        {
            $data['nationality2'] = "";            
        }else
        {
            $data['nationality2'] = $data['nationality']; 
            $data['nationality'] = "other"; 
        }

      
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {

     
            
       
            User::findOrFail($this->record->user_id)->update([
                'national_id' =>$data['national_id'],
                'gender' =>$data['gender'],
                'phone_number' =>$data['phone_number'],
                'email' =>$data['email'],
            ]);
            if($password =$data['password']) 
            {
                $data['password'] = bcrypt($password);
                User::findOrFail($this->record->user_id)->update(['password'=>$data['password']]);
            }
            $data['nationality'] = isset($data['nationality2']) ? $data['nationality2'] : $data['nationality'];

        return $data;

    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {     
        
            
            try{
                DB::beginTransaction();
                $Student = $record;
               
                unset($data['national_id']);
                $Student->user->update([
                    'gender' =>$data['gender'],
                    'phone_number' =>$data['phone_number'],
                    'email' =>$data['email'],
                    'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
                ]);
                foreach (['national_id','gender','phone_number','email','password'] as $key => $value) {
                    unset($data[$value]);
                }
                $semester  = $record->semesters()->orderBy('id', 'desc')->first();
                if($semester)
                {
                    $semester->update([
                        'semester_id' => $data['semester_id'],
                        'enrollment_date' => now(),
                        'is_current' => true,
                    ]);
                }
                else
                {
                    DB::table('student_semesters')->insert([
                        'student_id' => $Student->id,
                        'semester_id' => $data['semester_id'],
                        'enrollment_date' => now(),
                        'is_current' => true,
                    ]);
                }
                $Student->update([
                    "parent_id" =>$data['parent_id'],
                ]);
                DB::commit();
              

                $data = $Student->toArray();
                Notification::make()
                            ->title(trans('main.student_updated_successfully'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
            }
            catch(\Exception $ex)
            {
                DB::rollBack();
                        Notification::make()
                            ->title($ex->getMessage())
                            ->icon('heroicon-o-document-text')
                            ->iconColor('danger')
                            ->send();
            }
            $this->halt();
            return [];
        
        
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
