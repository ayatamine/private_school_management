<?php

namespace App\Filament\Student\Resources\StudentResource\Pages;


use MPDF;
use Filament\Forms;
use App\Models\User;
use Filament\Actions;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\TuitionFee;
use App\Models\ParentModel;
use Filament\Actions\Action;
use App\Models\ConcessionFee;
use App\Models\SchoolSetting;
use App\Models\ReceiptVoucher;
use Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ReceiptVoucherResource;
use App\Filament\Student\Resources\StudentResource;
use Filament\Actions\Concerns\InteractsWithActions;

class ViewMyProfile extends ViewRecord
{
    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.resources.students.pages.view-my-profile';
    protected function getHeaderActions(): array
    {
        return [

            Action::make('change_password')
                    ->color('warning')
                    ->closeModalByClickingAway(false)
                    ->label(trans('main.change_password'))
                    ->form([
                        
                        Forms\Components\TextInput::make(name: 'old_password')->label(trans('main.old_password'))
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make(name: 'password')->label(trans('main.new_password'))
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),
                    ])
                    ->action(function(array $arguments,array $data) {
                        try{
                            DB::beginTransaction();
                            $Student = Student::findOrFail($this->record->id);
                            if($data['password'] != "")
                            {
                                //check old password 
                                if (!Hash::check($data['old_password'],$Student?->user?->password)) {
                                    Notification::make()
                                    ->title(trans('main.current_password_wrong'))
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('danger')
                                    ->send();
                                    return ;
                                }
                                
                              $Student->user->update([
                                    'password' =>  bcrypt($data['password'])
                             ]);  
                            }
                            
                            DB::commit();
                            Notification::make()
                                                ->title(trans('main.password_updated_successfully'))
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
            Action::make('print_all_fees')
                    ->color('info')
                    ->label(trans('main.print_all_fees'))
                    ->url( route('print_pdf',['type'=>"invoice",'id'=>Invoice::whereStudentId($this->record->id)?->latest()?->first()?->id]))
                    
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
        $data['parent_relation']  = $parent?->parent_relation;
        $data['parent_national_id']  = $parent?->parent_national_id;
        $data['parent_email']  = $parent?->parent_email;
        $data['parent_phone_number']  = $parent?->parent_phone_number;
        $data['parent_gender']  = $parent?->parent_gender;

        return $data;
    }
    public function editPartitions(): Action
    {
        $concession_fees = ConcessionFee::active()->pluck('name','id')->toArray();
        $concession_fees[0] = 0;
  
        return Action::make('editPartitions')
        ->label(trans_choice('main.edit_partition',1))
        ->form([
                  //TODO: here you should put concession of the current year
                  
                  Forms\Components\Select::make('concession_fee_id')
                                ->label(trans_choice('main.concession_fee',1))
                                ->options($concession_fees)
                                ->required(),
        ])
        ->action(function (array $arguments,array $data) {
         
            $fee =$arguments['feeable_type']::findOrFail($arguments['fee_id']);
            
            $payment_partition = $fee->payment_partition;

            if($data['concession_fee_id'] == 0)
            {
                $discounts=$payment_partition;

                $discounts[0]['discount_type'] = "percentage";
                $discounts[0]['discount_value'] = 0;

                DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',[json_encode($discounts),$arguments['fee_id'],$arguments['feeable_type'],$this->record->id]);
            }
            else 
            {
       
                $concession_fee = ConcessionFee::findOrFail($data['concession_fee_id']);
               
                if(array_key_exists($arguments['partition'],$payment_partition))
                {
                
                    $discounts=$payment_partition[$arguments['partition']];
                    // foreach($payment_partition as $key=>$value)
                    // {
                    //     $discounts[$key] = $value;
                    // }
                    $discounts['discount_type'] = $concession_fee->type;
                    $discounts['discount_value'] = $concession_fee->value;
                    $payment_partition[$arguments['partition']] = $discounts;
                    dd($payment_partition);
                    dd($discounts);
                    DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?  AND student_id = ?',[json_encode($payment_partition),$arguments['fee_id'],$arguments['feeable_type'],$this->record->id]);
                }
            }
            Notification::make()
            ->title(trans('main.partition_updated_successfully'))
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
            return redirect(request()->header('Referer'));
            // dd($fee);
            // DB::update('update student_fee set name = ? where id = ?',[$name,$arguments['fee_id']]);
            
        });
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
    public function printReceipt(): Action
    {
        try{
        return Action::make('printReceipt')
                    // ->icon('icon-print')
                    ->color('primary')
                    ->label(trans('main.print'))
                    ->url(fn(array $arguments) => route('print_pdf',['type'=>"receipt_voucher",'id'=>$arguments['payment_id']]));
                    // ->action(function(array $arguments,array $data) {
                    //     $data = ['receipt' => ReceiptVoucher::find($arguments['payment_id']),'settings'=>SchoolSetting::first()];
                    //         $pdf = MPDF::loadView('pdf.receipt_voucher', $data);
                    //         $pdf->simpleTables = true;

                    //         $pdf->download('document.pdf');
                    //         header("Refresh:0");

                    // });
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }
    public function printAllPayments(): Action
    {
        try{
        return Action::make('printAllPayments')
                    ->icon('icon-print')
                    ->color('info')
                    ->label(trans('main.print_all_payments'))
                    ->url( route('print_pdf',['type'=>"all_payments",'id'=>$this->record->id]));
                    // ->action(function(array $arguments) {
                    //     $data = ['student' => $this->record,'settings'=>SchoolSetting::first()];
                    //         $pdf = MPDF::loadView('pdf.all_payments', $data);
                    //         $pdf->simpleTables = true;

                    //         $pdf->download('document.pdf');
                    //         header("Refresh:0");

                    // });
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }
    public function viewReceipt(): Action
    {
        try{
            
        return Action::make('viewReceipt')
                    // ->icon('icon-eye')
                    ->color('gray')
                    ->label(trans(key: 'main.view'))
                    ->action(function(array $arguments) {
                        return redirect(ReceiptVoucherResource::getUrl('view',['record'=>$arguments['payment_id']]));

                    });
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }
    public function editReceipt(): Action
    {
                try{
                    
                return Action::make('editReceipt')
                            ->label(trans(key: 'main.edit'))
                            // ->icon('icon-edit')
                             ->color('info')
                            ->action(function(array $arguments) {
                                     return redirect(ReceiptVoucherResource::getUrl('edit',['record'=>$arguments['payment_id']]));
                            });
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }
    public function deleteReceipt(): Action
    {
                try{
                    
                return Action::make('deleteReceipt')
                            // ->icon('icon-delete')
                            ->label(trans(key: 'main.delete'))
                             ->color('danger')
                             ->requiresConfirmation()
                            ->action(function(array $arguments) {
                                    ReceiptVoucher::findOrFail($arguments['payment_id'])->delete();
                                    Notification::make()
                                    ->title(trans('main.deleted_success'))
                                    ->icon(icon: 'heroicon-o-document-text')
                                    ->iconColor('danger')
                                    ->send();
                            });
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }
}
