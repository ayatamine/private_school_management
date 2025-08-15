<?php

namespace App\Filament\Resources\StudentResource\Pages;

use MPDF;
use Filament\Forms;
use App\Models\User;
use Filament\Actions;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Semester;
use App\Models\TuitionFee;
use App\Models\ParentModel;
use App\Models\AcademicYear;
use Filament\Actions\Action;
use App\Models\AcademicStage;
use App\Models\ConcessionFee;
use App\Models\SchoolSetting;
use App\Models\ReceiptVoucher;
use Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Traits\InteractWithStudentRecord;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\StudentResource;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ReceiptVoucherResource;
use Filament\Actions\Concerns\InteractsWithActions;

class ViewStudent extends ViewRecord  implements  HasActions,HasForms
{
    use InteractsWithActions; use InteractsWithForms;use InteractWithStudentRecord;
    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.resources.students.pages.view-student';
    protected function getHeaderActions(): array
    {
        return [
            Action::make('upgrade_student')
            ->color('success')
            ->label(trans('main.upgrade_student'))
            ->visible($this->record->termination_date == null && employeeHasPermission('upgrade_student_student'))
            ->form([
                Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                ->options(fn(Get $get) => $get('new_student') == true ? AcademicYear::where('is_registration_active',true)->where('is_upgrade_active',true)->pluck('name', 'id') : AcademicYear::where('is_upgrade_active',true)->pluck('name', 'id'))
                ->default(AcademicYear::where('is_registration_active',true)?->first()?->name)
                ->required()
                ->live(),
                Forms\Components\Select::make('academic_stage_id')->label(trans_choice('main.academic_stage',1))
                    ->options(fn(Get $get) => AcademicStage::whereHas('courses', function ($query) use ($get) {
                        $query->where('academic_year_id', $get('academic_year_id'));
                    })->pluck('name', 'id'))
                    ->required()
                    ->live(),
                Forms\Components\Select::make('course_id')->label(trans_choice('main.academic_course',1))
                    ->options(fn (Get $get): Collection => Course::query()
                    ->where('academic_stage_id', $get('academic_stage_id'))
                    ->pluck('name', key: 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set,) {
                        $set('semester_id', null);
                    })
                    ,
                Forms\Components\Select::make('semester_id')->label(trans_choice('main.semester',1))->required()
                    ->options(fn (Get $get): Collection => Semester::query()
                    ->where('course_id', $get('course_id'))
                    ->where('is_registration_active', true)
                    ->pluck('name', 'id'))->live(),
                Forms\Components\DatePicker::make('enrollment_date')->label(trans('main.enrollment_date'))->required(),
            ])
            ->action(function(array $arguments,array $data) {
                    //register it under a new semester
                    try{
                        DB::beginTransaction();
                        $this->record->semesters()->create([
                            'semester_id' => $data['semester_id'],
                            'enrollment_date' => $data['enrollment_date'],
                            'is_promoted' => true,
                            'is_current' => false,
                        ]);

                        DB::commit();
                        Notification::make()
                        ->title(trans('main.student_upgraded_successfully'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();
                    }
                    catch(\Exception $ex)
                    {
                        DB::rollBack();
                        Notification::make()
                        ->title($ex)
                        ->icon('heroicon-o-document-text')
                        ->iconColor('danger')
                        ->send();
                    }
                
            

            }),
            //if balance is ok
            // Action::make('termination1')
            // ->color('primary')
            // ->label(trans_choice('main.termination',1))
            // ->visible($this->record->termination_date == null && ($this->record->total_fees_rest !=0)  && !employeeHasPermission('create_student_termination_student::termination'))
            // ->modalContent(new HtmlString("<p class='font-semibold text-red-500'>".trans('main.student_termination_balance_error')."</p>"))
            // ->modalSubmitAction(false),
            Action::make('termination')
                    ->color('primary')
                    ->label(trans_choice('main.termination',1))
                    ->visible($this->record->termination_date == null || employeeHasPermission('create_student_termination_student::termination'))
                    ->form([
                        Forms\Components\DatePicker::make('termination_date')->label(trans('main.termination_date'))->required(),
                        Forms\Components\Textarea::make('termination_reason')->label(trans('main.termination_reason'))
                            ->columnSpanFull()
                            ->maxLength(26663)->required(),
                        Forms\Components\FileUpload::make(name: 'termination_document')->label(trans('main.document'))
                            ->columnSpanFull()
                            ->directory('termination_documents'),
                    ])
                    ->requiresConfirmation()
                    ->action(function(array $arguments,array $data) {
                        try{
                            DB::beginTransaction();
                            if($this->record->total_fees_rest != 0 && !employeeHasPermission('terminate_student_private_student::termination') )
                            {
                                Notification::make()
                                    ->title(trans('main.student_termination_balance_error'))
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('danger')
                                    ->send();
                                return;
                            }
                            $data['terminated_by'] = Auth::id();
                            $data['terminated_semester_id'] = $this->record->semester_id;
                            $this->record->update(['semester_id'=>null]);
                            //what this will return
                            $terminated_id = $this->record->termination()->create($data);
                            DB::commit();
                            Notification::make()
                                                ->title(trans('main.student_termination_success'))
                                                ->icon('heroicon-o-document-text')
                                                ->iconColor('success')
                                                ->send();
                            // return redirect()->route('filament.admin.resources.student-terminations.view', $terminated_id);
                        }
                        catch(\Exception $ex)
                        {
                            DB::rollBack();
                            Notification::make()
                            ->title($ex)
                            ->icon('heroicon-o-document-text')
                            ->iconColor('danger')
                            ->send();
                        }
                        
                    

                    }),
            // Action::make('print_all_fees')
            //         ->color('info')
            //         ->label(trans('main.print_all_fees'))
            //         ->visible(employeeHasPermission('print_fees_invoice_student'))
            //         ->url( route('print_pdf',['type'=>"invoice",'id'=>Invoice::whereStudentId($this->record->id)?->latest()?->first()?->id]))
                    
        ];
    }
    public function getFormStatePath(): string
    {
        return 'form';
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;

        $parent = ParentModel::find($data['parent_id']);
        // $data['parent_relation']  = '$parent?->parent_relation';
        $data['parent_national_id']  = $parent?->parent_national_id;
        $data['parent_email']  = $parent?->parent_email;
        $data['parent_phone_number']  = $parent?->parent_phone_number;
        $data['parent_gender']  = $parent?->parent_gender;

        return $data;
    }
   
    // public function editTransportFeePartitions(): Action
    // {
    //     return Action::make('editTransportFeePartitions')
    //     ->label(trans_choice('main.edit_partition',1))
    //     ->form([
    //          //TODO: here you should put concession of the current year
    //               Forms\Components\Select::make('concession_fee_id')
    //                             ->label(trans_choice('main.concession_fee',1))
    //                             ->options([...ConcessionFee::active()->pluck('name','id'),0])
    //                             ->required(),
    //     ])
    //     ->action(function (array $arguments,array $data) {
    //         $concession_fee = ConcessionFee::findOrFail($data['concession_fee_id']);
    //         $fee =TuitionFee::findOrFail($arguments['fee_id']);
    //         $payment_partition = $fee->payment_partition;
    //         if(array_key_exists($arguments['partition'],$payment_partition))
    //         {
               
    //             $discounts=$payment_partition;
    //             // foreach($payment_partition as $key=>$value)
    //             // {
    //             //     $discounts[$key] = $value;
    //             // }
    //             $discounts[0]['discount_type'] = $concession_fee->type;
    //             $discounts[0]['discount_value'] = $concession_fee->value;

    //             DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?',[json_encode($discounts),$arguments['fee_id'],$arguments['feeable_type']]);
    //         }
    //         Notification::make()
    //         ->title(trans('main.partition_updated_successfully'))
    //         ->icon('heroicon-o-document-text')
    //         ->iconColor('success')
    //         ->send();
    //         return redirect(request()->header('Referer'));
    //         // dd($fee);
    //         // DB::update('update student_fee set name = ? where id = ?',[$name,$arguments['fee_id']]);
            
    //     });
    // }
    
}
