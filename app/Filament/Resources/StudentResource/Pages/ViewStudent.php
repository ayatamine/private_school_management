<?php

namespace App\Filament\Resources\StudentResource\Pages;

use MPDF;
use Filament\Forms;
use App\Models\User;
use Filament\Actions;
use App\Models\Student;
use App\Models\TuitionFee;
use App\Models\ParentModel;
use Filament\Actions\Action;
use App\Models\ConcessionFee;
use App\Models\SchoolSetting;
use App\Models\ReceiptVoucher;
use Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Contracts\HasActions;
use App\Filament\Resources\StudentResource;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;

class ViewStudent extends ViewRecord  implements  HasActions,HasForms
{
    use InteractsWithActions; use InteractsWithForms;
    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.resources.students.pages.view-student';
    protected function getHeaderActions(): array
    {
        return [
            Action::make('print_all_fees')
                    ->color('info')
                    ->label(trans('main.print_all_fees'))
                    ->action(function(array $arguments,array $data) {
                        
                        $data = ['student' => $this->record,'settings'=>SchoolSetting::first()];
                            $pdf = MPDF::loadView('pdf.all_fees', $data);
                            $pdf->simpleTables = true;

                            $pdf->download(`all_fees_for_student_{{$this->record->username}}.pdf`);
                            header("Refresh:0");

                    })
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
        return Action::make('editPartitions')
        ->label(trans_choice('main.edit_partition',1))
        ->form([
             //TODO: here you should put concession of the current year
                  Forms\Components\Select::make('concession_fee_id')
                                ->label(trans_choice('main.concession_fee',1))
                                ->options([...ConcessionFee::active()->pluck('name','id'),0])
                                ->required(),
        ])
        ->action(function (array $arguments,array $data) {
            $fee =TuitionFee::findOrFail($arguments['fee_id']);
            $payment_partition = $fee->payment_partition;
            if($data['concession_fee_id'] == 0)
            {
                $discounts=$payment_partition;

                $discounts[0]['discount_type'] = "percentage";
                $discounts[0]['discount_value'] = 0;

                DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?',[json_encode($discounts),$arguments['fee_id'],$arguments['feeable_type']]);
            }
            else 
            {
                $concession_fee = ConcessionFee::findOrFail($data['concession_fee_id']);
                
                if(array_key_exists($arguments['partition'],$payment_partition))
                {
                
                    $discounts=$payment_partition;
                    // foreach($payment_partition as $key=>$value)
                    // {
                    //     $discounts[$key] = $value;
                    // }
                    $discounts[0]['discount_type'] = $concession_fee->type;
                    $discounts[0]['discount_value'] = $concession_fee->value;

                    DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?',[json_encode($discounts),$arguments['fee_id'],$arguments['feeable_type']]);
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
    public function editTransportFeePartitions(): Action
    {
        return Action::make('editTransportFeePartitions')
        ->label(trans_choice('main.edit_partition',1))
        ->form([
             //TODO: here you should put concession of the current year
                  Forms\Components\Select::make('concession_fee_id')
                                ->label(trans_choice('main.concession_fee',1))
                                ->options([...ConcessionFee::active()->pluck('name','id'),0])
                                ->required(),
        ])
        ->action(function (array $arguments,array $data) {
            $concession_fee = ConcessionFee::findOrFail($data['concession_fee_id']);
            $fee =TuitionFee::findOrFail($arguments['fee_id']);
            $payment_partition = $fee->payment_partition;
            if(array_key_exists($arguments['partition'],$payment_partition))
            {
               
                $discounts=$payment_partition;
                // foreach($payment_partition as $key=>$value)
                // {
                //     $discounts[$key] = $value;
                // }
                $discounts[0]['discount_type'] = $concession_fee->type;
                $discounts[0]['discount_value'] = $concession_fee->value;

                DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?',[json_encode($discounts),$arguments['fee_id'],$arguments['feeable_type']]);
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
    public function printReceipt(): Action
    {
        try{
        return Action::make('printReceipt')
                    ->icon('icon-print')
                    ->color('primary')
                    ->label(trans('main.print_receipt_voucher'))
                    ->action(function(array $arguments,array $data) {
                        $data = ['receipt' => ReceiptVoucher::find($arguments['payment_id']),'settings'=>SchoolSetting::first()];
                            $pdf = MPDF::loadView('pdf.receipt_voucher', $data);
                            $pdf->simpleTables = true;

                            $pdf->download('document.pdf');
                            header("Refresh:0");

                    });
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }
}
