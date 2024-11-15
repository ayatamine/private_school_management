<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Semester;
use App\Models\GeneralFee;
use App\Models\TuitionFee;
use App\Models\ParentModel;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\StudentResource;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ViewAction::make(),
          
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {   
  
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;

        $data['birt_date'] = date('Y-m-d' ,strtotime($data['birth_date']));

        if( $data['semester_id'])
        {
            $semester = Semester::find($data['semester_id']);
            $data['academic_year_id'] = $semester->academic_year_id;
            $data['academic_stage_id'] = $semester->course->academic_stage_id;
            $data['course_id'] = $semester->course_id;

        }
       
        
        $parent = ParentModel::find($data['parent_id']);
        $data['parent_relation']  = $parent?->relation ;
        $data['parent_national_id']  = $parent?->user->national_id;
        $data['parent_email']  = $parent?->user->email;
        $data['parent_phone_number']  = $parent?->user->phone_number;
        $data['parent_gender']  = $parent?->user->gender ? trans("main.".$parent?->user?->gender."") : "";

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

 
           try{
          
            DB::beginTransaction();
            User::findOrFail($this->record->user_id)->update([
                'national_id' =>$data['national_id'],
                'gender' =>$data['gender'],
                'phone_number' =>$data['phone_number'],
                'email' =>$data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
            ]);
            
            $data['nationality'] = $data['nationality'] =="saudian" ? $data['nationality'] : $data['nationality2'];
            $this->record->update($data);
                // add tuiton fees
                $tuitionFee = TuitionFee::whereCourseId($data['course_id'])->first();
                if($tuitionFee)
                {
                    $this->record->tuitionFees()->sync($tuitionFee->id);
                }
                // add other fees
                $general = GeneralFee::whereCourseId($data['course_id'])->first();
                if($general)
                {   
                    $this->record->otherFees()->sync($general->id);
                }
                // add concession fees
               
                $discounts = $tuitionFee->payment_partition;
               
                $discounts[0]['discount_type'] = "percentage";
                $discounts[0]['discount_value'] = 0;
                
                if($tuitionFee) DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',[json_encode($discounts),$tuitionFee->id,TuitionFee::class,$this->record->id]);
                if($general) DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',[json_encode($discounts),$general->id,GeneralFee::class,$this->record->id]);
              
                //create invoice for student
                $academic_year_id = $this->record->semester?->academicYear?->id;
                $invoice  = Invoice::whereStudentId($this->record->id)->whereAcademicYearId($academic_year_id)->first();
                if(!$invoice)
                {
                    $invoice =Invoice::create([
                        'number'=>$this->record->semester?->academicYear?->name."".$this->record->registration_number,
                        'name' => trans('main.fees_invoice')." ".$this->record->semester?->academicYear?->name,
                        'student_id'=>$this->record->id,
                        'academic_year_id'=>$academic_year_id,
                    ]);
                    $this->record->invoices()->save($invoice);
                }
                DB::commit();     
                Notification::make()
                    ->title(trans('main.student_updated_successfully'))
                    ->icon('heroicon-o-document-text')
                    ->iconColor('success')
                    ->send();
                
           }
           catch(\Exception $ex)
           {
             throw $ex;
             DB::rollBack();
             Notification::make()
                    ->title(trans('main.error_on_student_status'))
                    ->icon('heroicon-o-document-text')
                    ->iconColor('danger')
                    ->send();
           }

            // $this->halt();
        return $data;

    }
}
