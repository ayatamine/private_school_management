<?php

namespace App\Traits;

use App\Models\File;
use App\Models\Invoice;
use Filament\Actions\Action;
use App\Models\ConcessionFee;
use App\Models\ReceiptVoucher;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Filament\Resources\ReceiptVoucherResource;
use Illuminate\Database\Eloquent\Relations\MorphMany;


Trait InteractWithStudentRecord
{
    public function editPartitions(): Action
    {
        $concession_fees = ConcessionFee::active()->pluck('name','id')->toArray();
        $concession_fees[0] = 0;
  
        return Action::make('editPartitions')
        ->label(trans_choice('main.edit_partition',1))
        ->form([
                  //TODO: here you should put concession of the current year
                  
                  Select::make('concession_fee_id')
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

                // foreach ($payment_partition as $k=> $ppartition) {
                    // if($arguments['partition'] == $k)
                    // {
                    
                        // $discounts=$payment_partition[$arguments['partition']];
                        // foreach($payment_partition as $key=>$value)
                        // {
                        //     $discounts[$key] = $value;
                        // }
                        // $discounts['discount_type'] = $concession_fee->type;
                        // $discounts['discount_value'] = $concession_fee->value;
                        // $payment_partition[$arguments['partition']] = $discounts;
                        // dd($payment_partition);
                        // dd($discounts);
                        $existing_discounts = DB::table('student_fee')->where('feeable_id',$arguments['fee_id'])->where('student_id',$this->record->id)->where('feeable_type',$arguments['feeable_type'])->first();
                    
                        $existing_discounts_decoded = json_decode($existing_discounts->discounts,true);
                        if($existing_discounts_decoded && array_key_exists($arguments['partition'],$existing_discounts_decoded))
                        {

                            $existing_discounts_decoded[$arguments['partition']]['discount_type'] =$concession_fee->type;
                            $existing_discounts_decoded[$arguments['partition']]['discount_value'] =$concession_fee->value;
                            DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?  AND student_id = ?',[json_encode($existing_discounts_decoded),$arguments['fee_id'],$arguments['feeable_type'],$this->record->id]);
                       
                        }
                        else 
                        {
                           
                            $discounts=$payment_partition[$arguments['partition']];
                            $discounts['discount_type'] = $concession_fee->type;
                            $discounts['discount_value'] = $concession_fee->value;
                            // array_push($existing_discounts_decoded,$discounts);
                            DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?  AND student_id = ?',[json_encode($discounts),$arguments['fee_id'],$arguments['feeable_type'],$this->record->id]);
                        }
                    }
                    // else
                    // {
                    //     dd('sdf');
                    //     $existing_discounts = DB::table('student_fee')->where('feeable_id',$arguments['fee_id'])->where('student_id',$this->record->id)->where('feeable_type',$arguments['feeable_type'])->first();
                    //     $existing_discounts_decoded = json_decode($existing_discounts->discounts,true)[$k];
                    //     if(isset($existing_discounts_decoded[$k]) && !array_key_exists('discount_type',$existing_discounts_decoded[$k]))
                    //     {
                    //         $discounts=$existing_discounts_decoded[$k];
                    //         $discounts['discount_type'] = "value";
                    //         $discounts['discount_value'] = 0;
                    //         $payment_partition[$arguments['partition']] = $discounts;
                    //     }
                       
                        

                        
                    //     DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?  AND student_id = ?',[json_encode($payment_partition),$arguments['fee_id'],$arguments['feeable_type'],$this->record->id]);
                    // }
                // }
                
            // }
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
    public function showFees(): Action
    {
                try{
                    
                return Action::make('showFees')
                            // ->icon('icon-delete')
                            ->label(trans(key: 'main.show_fees'))
                             ->action(function () {
                                $this->redirect(
                                    route('filament.admin.pages.student-fees', ['record' => $this->record->id])
                                );  
                            });     
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }
    public function printInvoice(): Action
    {
                try{
                    
                return Action::make('printInvoice')
                            // ->icon('icon-delete')
                            ->color('success')
                            ->label(trans(key: 'main.print_fees'))
                             ->action(function () {
                                $this->redirect(
                                    route('print_pdf',['type'=>"invoice",'id'=>Invoice::whereStudentId($this->record->id)?->latest()?->first()?->id])
                                );  
                            });     
                }
                catch(\Exception $ex)
                {
                    dd($ex);
                }
    }

}