<?php

namespace App\Filament\Resources\NewestStudentResource\Pages;



use App\Models\User;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\TuitionFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\NewestStudentResource;
class CreateNewestStudent extends CreateRecord
{
    protected static string $resource = NewestStudentResource::class;
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
            
            try{
                DB::beginTransaction();
                $Student = Student::findOrFail(intval($data['registration_number']));
                $Student->user->update([
                    'national_id' =>$data['national_id'],
                    'gender' =>$data['gender'],
                    'phone_number' =>$data['phone_number'],
                    'email' =>$data['email'],
                    'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
                ]);
                foreach (['national_id','gender','phone_number','email','password'] as $key => $value) {
                    unset($data[$value]);
                }
                $Student->update([
                    "created_at" =>$data['created_at'],
                    "academic_year_id" =>$data['academic_year_id'],
                    "semester_id" =>$data['semester_id'],
                    "parent_id" =>$data['parent_id'],
                    // "opening_balance" =>$data['opening_balance'],
                    // "finance_document" =>$data['finance_document'],
                    // "note" =>$data['note'],
                ]);
                DB::commit();
                DB::beginTransaction();
                //add tuiton fees
                // $tuitionFee = TuitionFee::whereCourseId($Student?->semester()?->course_id)->first();
                // if($tuitionFee)
                // {
                //     $Student->tuitionFees()->sync($tuitionFee->id);
                // }
                //  //create invoice for student
                // $academic_year_id = $data['academic_year_id'];
                // $invoice  = Invoice::whereStudentId($Student->id)->whereAcademicYearId($academic_year_id)->first();
                // if(!$invoice)
                // {
                //     $invoice =Invoice::create([
                //         'number'=>$Student->semester?->academicYear?->name."".$Student->registration_number,
                //         'name' => trans('main.fees_invoice')." ".$Student->course?->academicYear?->name,
                //         'student_id'=>$Student->id,
                //         'academic_year_id'=>$academic_year_id,
                //     ]);
                //     $Student->invoices()->save($invoice);
                // }

                $data = $Student->toArray();
                Notification::make()
                            ->title(trans('main.student_registered_successfully'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
                DB::commit();
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
        
    }
}
