<?php

use Carbon\Carbon;
use Filament\Forms;
use App\Models\File;
use Filament\Tables;
use Filament\Actions;
use App\Models\Invoice;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Employee;
use App\Models\GeneralFee;
use App\Models\StudentTermination;
use App\Models\TuitionFee;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use App\Models\Transport;
use App\Models\ValueAddedTax;

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
}
if(!function_exists('approve_reject_action'))
{
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

if(!function_exists('calculateTuitionFees'))
{
    function calculateTuitionFees($record)
    {
        $grand_total = $total = $value_after_discount = $value_after_tax = $total_fees_to_pay = [];
        $academic_years = [];
        
        if ($record->tuitionFees != null && count($record->tuitionFees)) {
            foreach ($record->tuitionFees as $k => $fee) {
                if (count($fee->payment_partition)) {
                    foreach ($fee->payment_partition as $i => $partition) {
                        // Track academic years
                        if (!in_array($fee->academicYear?->name, $academic_years)) {
                            $academic_years[] = $fee->academicYear?->name;
                        }

                        // Adjust value based on approval/termination dates
                        $partitionValue = $partition['value'];
                        if ($record->approved_at && ($partition['due_date_end_at'] < Carbon::createFromTimestamp($record->approved_at)->format('Y-m-d'))) {
                            $partitionValue = 0;
                        }
                        if ($record->termination_date && $record->termination_date < $partition['due_date']) {
                            $partitionValue = 0;
                        }

                        // Get discounts
                        $discounts = DB::table('student_fee')
                            ->where('student_id', $record->id)
                            ->where('feeable_id', $fee->id)
                            ->where('feeable_type', 'App\Models\TuitionFee')
                            ->value('discounts');
                        $decodedDiscounts = json_decode($discounts, true);

                        // Calculate discount
                        $valueAfterDiscount = $partitionValue;
                        if (isset($decodedDiscounts[$i]) && array_key_exists('discount_value', $decodedDiscounts[$i])) {
                            if ($decodedDiscounts[$i]['discount_type'] == 'percentage') {
                                $valueAfterDiscount = $partitionValue * (1 - ($decodedDiscounts[$i]['discount_value'] / 100));
                            } else {
                                $valueAfterDiscount = $partitionValue - $decodedDiscounts[$i]['discount_value'];
                            }
                        }
                        $value_after_discount[$i] = $valueAfterDiscount;

                        // Calculate tax
                        $taxValue = 0;
                        if ($record->nationality != "saudian") {
                            $vat = ValueAddedTax::whereDate('applies_at', "<=", date('Y-m-d', strtotime($partition['due_date_end_at'])))
                                ->first();
                            if ($vat == null) {
                                $vat = ValueAddedTax::first();
                            }
                            $taxValue = ($vat?->percentage ? $vat?->percentage : 0) / 100 * $valueAfterDiscount;
                        }
                        $value_after_tax[$i] = $taxValue;

                        // Calculate totals
                        $total[$i] = $valueAfterDiscount + $taxValue;
                        $total_fees_to_pay[$i] = (now() > $partition['due_date']) ? $total[$i] : 0;
                    }
                    $grand_total[$k] = array_sum($total);
                }
            }

            return [
                'academic_years' => $academic_years,
                'grand_total' => array_sum($grand_total),
                'total_fees_to_pay' => array_sum($total_fees_to_pay),
                'summary' => (object) [
                    'academic_year' => count($academic_years) > 0 ? $academic_years[0] : null,
                    'total' => number_format(array_sum($grand_total), 2, '.', ','),
                    'total_fees_to_pay' => number_format(array_sum($total_fees_to_pay), 2, '.', ','),
                ]
            ];
        }

        return null;
    }
}
if(!function_exists('calculateTransportFees'))
{
    function calculateTransportFees($record)
    {
        $total = $value_after_discount = $value_after_tax = $total_fees_to_pay = [];
        
        if ($record->transportFees != null && count($record->transportFees)) {
            foreach ($record->transportFees as $fee) {
                if (count($fee->payment_partition)) {
                    foreach ($fee->payment_partition as $i => $partition) {
                        // Skip if transport terminated before due date
                        $transport = $record->transport;
                        if ($transport && ($transport->termination_date != null && $transport->termination_date <= $partition['due_date_end_at'])) {
                            continue;
                        }

                        // Adjust value based on dates
                        $partitionValue = $partition['value'];
                        if (Transport::whereStudentId($record->id)?->first()?->created_at >= $partition['due_date_end_at']) {
                            $partitionValue = 0;
                        }
                        if ($record->termination_date && $record->termination_date <= $partition['due_date']) {
                            $partitionValue = 0;
                        }

                        // Get discounts
                        $discounts = DB::table('student_fee')
                            ->where('student_id', $record->id)
                            ->where('feeable_id', $fee->id)
                            ->where('feeable_type', 'App\Models\TransportFee')
                            ->value('discounts');
                        $decodedDiscounts = json_decode($discounts, true);

                        // Calculate discount
                        $valueAfterDiscount = $partitionValue;
                        if (isset($decodedDiscounts[$i]) && array_key_exists('discount_value', $decodedDiscounts[$i])) {
                            if ($decodedDiscounts[$i]['discount_type'] == 'percentage') {
                                $valueAfterDiscount = $partitionValue * (1 - ($decodedDiscounts[$i]['discount_value'] / 100));
                            } else {
                                $valueAfterDiscount = $partitionValue - $decodedDiscounts[$i]['discount_value'];
                            }
                        }
                        $value_after_discount[$i] = $valueAfterDiscount;

                        // Calculate tax
                        $vat = ValueAddedTax::whereDate('applies_at', "<=", date('Y-m-d', strtotime($partition['due_date_end_at'])))
                            ->first() ?? ValueAddedTax::first();
                        
                        $taxValue = ($vat?->percentage ? $vat?->percentage : 0) / 100 * $valueAfterDiscount;
                        $value_after_tax[$i] = $taxValue;

                        // Calculate totals
                        $total[$i] = $valueAfterDiscount + $taxValue;
                        $total_fees_to_pay[$i] = (now() > $partition['due_date']) ? $total[$i] : 0;
                    }
                }
            }

            return [
                'total' => array_sum($total),
                'total_fees_to_pay' => array_sum($total_fees_to_pay),
                'formatted' => [
                    'total' => number_format(array_sum($total), 2, '.', ','),
                    'total_fees_to_pay' => number_format(array_sum($total_fees_to_pay), 2, '.', ','),
                ]
            ];
        }

        return null;
    }
}
if(!function_exists('calculateGeneralFees'))
{
    function calculateGeneralFees($record)
    {
        $grand_total = $total = $value_after_discount = $value_after_tax = $total_fees_to_pay = [];
        
        if ($record->otherFees != null && count($record->otherFees)) {
            foreach ($record->otherFees as $k => $fee) {
                if (count($fee->payment_partition)) {
                    foreach ($fee->payment_partition as $i => $partition) {
                        // Skip if student terminated before due date
                        if ($record->termination_date && $record->termination_date <= $partition['due_date_end_at']) {
                            continue;
                        }

                        // Adjust value based on approval/termination dates
                        $partitionValue = $partition['value'];
                        if ($record->approved_at && ($partition['due_date_end_at'] <= Carbon::createFromTimestamp($record->approved_at)->format('Y-m-d'))) {
                            $partitionValue = 0;
                        }
                        if ($record->termination_date && $record->termination_date <= $partition['due_date']) {
                            $partitionValue = 0;
                        }

                        // Get discounts
                        $discounts = DB::table('student_fee')
                            ->where('student_id', $record->id)
                            ->where('feeable_id', $fee->id)
                            ->where('feeable_type', 'App\Models\GeneralFee')
                            ->value('discounts');
                        $decodedDiscounts = json_decode($discounts, true);

                        // Calculate discount
                        $valueAfterDiscount = $partitionValue;
                        if (isset($decodedDiscounts[$i]) && array_key_exists('discount_value', $decodedDiscounts[$i])) {
                            if ($decodedDiscounts[$i]['discount_type'] == 'percentage') {
                                $valueAfterDiscount = $partitionValue * (1 - ($decodedDiscounts[$i]['discount_value'] / 100));
                            } else {
                                $valueAfterDiscount = $partitionValue - $decodedDiscounts[$i]['discount_value'];
                            }
                        }
                        $value_after_discount[$i] = $valueAfterDiscount;

                        // Calculate tax
                        $vat = ValueAddedTax::whereDate('applies_at', "<=", date('Y-m-d', strtotime($partition['due_date_end_at'])))
                            ->first() ?? ValueAddedTax::first();
                        
                        $taxValue = ($vat?->percentage ? $vat?->percentage : 0) / 100 * $valueAfterDiscount;
                        $value_after_tax[$i] = $taxValue;

                        // Calculate totals
                        $total[$i] = $valueAfterDiscount + $taxValue;
                        $total_fees_to_pay[$i] = (now() > $partition['due_date']) ? $total[$i] : 0;
                    }
                    $grand_total[$k] = array_sum($total);
                }
            }

            return [
                'grand_total' => array_sum($grand_total),
                'total_fees_to_pay' => array_sum($total_fees_to_pay),
                'formatted' => [
                    'grand_total' => number_format(array_sum($grand_total), 2, '.', ','),
                    'total_fees_to_pay' => number_format(array_sum($total_fees_to_pay), 2, '.', ','),
                ]
            ];
        }

        return null;
    }
}
if(!function_exists('calculateAllFees'))
{
    function calculateAllFees($student)
    {
        // Calculate each fee type
        $tuitionFees = calculateTuitionFees($student);
        $transportFees = calculateTransportFees($student);
        $generalFees = calculateGeneralFees($student);
    
        // Calculate grand totals
        $totalTuition = $tuitionFees['grand_total'] ?? 0;
        $totalTransport = $transportFees['total'] ?? 0;
        $totalGeneral = $generalFees['grand_total'] ?? 0;
        
        $totalFeesToPayTuition = $tuitionFees['total_fees_to_pay'] ?? 0;
        $totalFeesToPayTransport = $transportFees['total_fees_to_pay'] ?? 0;
        $totalFeesToPayGeneral = $generalFees['total_fees_to_pay'] ?? 0;
    
        $grandTotal = $totalTuition + $totalTransport + $totalGeneral;
        $totalFeesToPay = $totalFeesToPayTuition + $totalFeesToPayTransport + $totalFeesToPayGeneral;
        
        return [
            'tuition' => $tuitionFees,
            'transport' => $transportFees,
            'general' => $generalFees,
            'totals' => [
                'grand_total' => $grandTotal,
                'total_fees_to_pay' => $totalFeesToPay,
                'formatted' => [
                    'grand_total' => number_format($grandTotal, 2, '.', ','),
                    'total_fees_to_pay' => number_format($totalFeesToPay, 2, '.', ','),
                ]
            ],
            'breakdown' => [
                'tuition' => $totalTuition,
                'transport' => $totalTransport,
                'general' => $totalGeneral,
                'formatted' => [
                    'tuition' => number_format($totalTuition, 2, '.', ','),
                    'transport' => number_format($totalTransport, 2, '.', ','),
                    'general' => number_format($totalGeneral, 2, '.', ','),
                ]
            ]
        ];
    }

}
if(!function_exists('reverse_number_format'))
{
    function reverse_number_format($formatted_number, $decimal_separator = ',', $thousands_separator = ',') {
        $number = str_replace($thousands_separator, '', $formatted_number);
        
        $number = str_replace($decimal_separator, '.', $number);
        
        return (float)$number;
    }
}
if(!function_exists('approve_reject_student_termination'))
{
    
    function approve_reject_student_termination($is_table_action=true):Tables\Actions\Action | \Filament\Infolists\Components\Actions\Action
    {
        $form =[
            Forms\Components\DatePicker::make(name: 'termination_approval_date')->label(trans('main.termination_approval_date'))
            ->default(fn(StudentTermination $studentTermination)=>$studentTermination->termination_approval_date)
            ->required()
            ->hidden(fn(Get $get)=>$get('status') == "rejected"),  
        ];
        if($is_table_action) 
        {
            return    Tables\Actions\Action::make('registeration_action') 
                        ->label(trans('main.registeration_action'))
                        ->visible(fn(StudentTermination $studentTermination)=>$studentTermination->termination_approval_date == null)
                        ->icon('heroicon-o-check')
                        ->color('primary')
                        ->form($form)
                        ->requiresConfirmation()
                        ->closeModalByClickingAway(false)
                        ->action(fn(StudentTermination $StudentTermination, array $data)=>complete_termination_process($StudentTermination, $data));
        }

        return   \Filament\Infolists\Components\Actions\Action::make('registeration_action')
        ->label(trans('main.registeration_action'))
        ->visible(employeeHasPermission('approve_registeration_newest::student'))
        ->icon('heroicon-o-check')
        ->color('primary')
        ->form($form)
        ->action(fn(StudentTermination $StudentTermination, array $data)=>complete_termination_process($StudentTermination, $data));
    }
}
if(!function_exists('complete_termination_process'))
{
    function complete_termination_process(StudentTermination $StudentTermination,array $data){
            dd('sdfsd');
        try{
            DB::beginTransaction();
            $StudentTermination->update($data);
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