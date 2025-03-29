<?php

use Filament\Forms;
use App\Models\File;
use Filament\Tables;
use Filament\Actions;
use App\Models\Invoice;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Employee;
use App\Models\GeneralFee;
use App\Models\TuitionFee;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;

if(!function_exists('employeeHasPermission'))
{
     function employeeHasPermission($permission):bool
    { 
        return  (auth()->user()->hasRole('super_admin')) || (auth()->user()?->employee && auth()->user()?->employee->hasPermissionTo($permission));
    }
}
if(!function_exists('addFile'))
{
     function addFile($record,$directory):Action
    { 
        return Actions\Action::make('add_files')
        ->color('info')
        ->label(trans('main.add_files'))
        ->closeModalByClickingAway(false)
        ->form([
            FileUpload::make('file')
                ->label(trans('main.files'))
                ->directory($directory)
                ->preserveFileNames(),
            Textarea::make('description')->label(trans('main.description')),
        ])
        ->action(function (array $data,$record) {

            if($data['file'])
            {
                    // foreach($data['files'] as $file)
                    // {
                        //add here extension check if it is image or a file
                        $extension = strtolower(pathinfo($data['file'], PATHINFO_EXTENSION));

                        // Determine the file type based on the extension
                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif','webp','pmp','svg'])) {
                            $fileType = 'image';
                        } elseif (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx','txt','odt','pptx'])) {
                            $fileType = 'document';
                        } 
                        else {
                            Notification::make()
                            ->title(trans('main.file_type_is_not_supported'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('danger')
                            ->send();
                            return; 
                        }

                        $albumFile = new File();
                        $albumFile->fileable_id = $record->id;
                        $albumFile->fileable_type = Employee::class;
                        $albumFile->type = $fileType; // Set type based on file type
                        $albumFile->file = $data['file'];
                        $albumFile->description = $data['description'];
                        $albumFile->save();
                    // }
            }
            Notification::make()
            ->title(trans('main.file_added_successfully'))
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
        });
    }
}
if(!function_exists('print_table_resrource_list'))
{
    function print_table_resrource_list($permission_name,$route):Action
    {
        return  Actions\Action::make('print_table')
        ->icon('icon-print')
        ->color('info')
        ->label(trans('main.print'))
        ->visible(employeeHasPermission($permission_name))
        ->url($route);
    }
}
if(!function_exists('approve_reject_student'))
{
    
    function approve_reject_student($permission_name,$is_table_action=true):Tables\Actions\Action | \Filament\Infolists\Components\Actions\Action
    {
        $form =[
            Forms\Components\Select::make(name: 'status')->label(trans('main.approvel_status'))
            ->options(['approved'=>trans('main.approve'), 'rejected'=>trans('main.reject')])
            ->default(fn(Student $student)=>$student->status == "approved" ? "approved" : "pending")
            ->live()
            ->required(),  
            Forms\Components\DatePicker::make(name: 'approved_at')->label(trans('main.approvel_date'))
            ->default(fn(Student $student)=>$student->status == "approved" ? $student->approved_at : null)
            ->required()
            ->hidden(fn(Get $get)=>$get('status') == "rejected"),  
        ];
        if($is_table_action) 
        {
            return    Tables\Actions\Action::make('registeration_action') 
                        ->label(trans('main.registeration_action'))
                        ->visible(employeeHasPermission('approve_registeration_newest::student'))
                        ->icon('heroicon-o-check')
                        ->color('primary')
                        ->form($form)
                        ->action(fn(Student $Student, array $data)=>approve_reject_action($Student, $data));
        }

        return   \Filament\Infolists\Components\Actions\Action::make('registeration_action')
        ->label(trans('main.registeration_action'))
        ->visible(employeeHasPermission('approve_registeration_newest::student'))
        ->icon('heroicon-o-check')
        ->color('primary')
        ->form($form)
        ->action(fn(Student $Student, array $data)=>approve_reject_action($Student, $data));
    }
    function approve_reject_action(Student $Student,array $data){
            
        try{
            DB::beginTransaction();
            $Student->update($data);
            if($data['status'] == "approved")
            {
                // add tuiton fees
                $tuitionFee = TuitionFee::whereCourseId($Student?->semester?->course_id)->first();
                if(!$Student?->semester)
                {
                    Notification::make()
                        ->title(trans('main.student_not_yet_attached_to_course'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('danger')
                        ->send();
                    return redirect()->route('filament.admin.resources.newest-students.edit',['record'=>$Student?->id]);
                }
                if($tuitionFee)
                {
                    $Student->tuitionFees()->sync($tuitionFee->id);
                }
                // add other fees
                // add other fees
                $generalFees = GeneralFee::whereCourseId($Student?->semester?->course_id)->get();
                if($generalFees)
                {
                    foreach($generalFees as $fee)
                    {
                        $Student->otherFees()->sync($fee->id);
                        $discounts = $fee->payment_partition;
                        $discounts[0]['discount_type'] = "percentage";
                        $discounts[0]['discount_value'] = 0;
                        DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',[json_encode($discounts),$fee->id,GeneralFee::class,$Student->id]);

                    }
                    
                }
                // add concession fees
            
                $discounts = $tuitionFee->payment_partition;
               
                $discounts[0]['discount_type'] = "percentage";
                $discounts[0]['discount_value'] = 0;
                
                if($tuitionFee) DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',[json_encode($discounts),$tuitionFee->id,TuitionFee::class,$Student->id]);
                
              
                //create invoice for student
                $academic_year_id = $Student->semester?->academicYear?->id;
                $invoice  = Invoice::whereStudentId($Student->id)->whereAcademicYearId($academic_year_id)->first();
                if(!$invoice)
                {
                    $invoice =Invoice::create([
                        'number'=>$Student->semester?->academicYear?->name."".$Student->registration_number,
                        'name' => trans('main.fees_invoice')." ".$Student->semester?->academicYear?->name,
                        'student_id'=>$Student->id,
                        'academic_year_id'=>$academic_year_id,
                    ]);
                    $Student->invoices()->save($invoice);
                }
            }
                Notification::make()
                    ->title(trans('main.student_status_changed_successfully'))
                    ->icon('heroicon-o-document-text')
                    ->iconColor('success')
                    ->send();
                DB::commit();
            
        }
        catch(Exception $ex)
        {
            DB::rollBack();
            dd($ex);
            Notification::make()
                ->title($ex)
                ->icon('heroicon-o-document-text')
                ->iconColor('danger')
                ->send();
        }
    }
}