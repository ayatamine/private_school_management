<?php

namespace App\Filament\Resources\TransferResource\Pages;

use Filament\Actions;
use App\Models\Transfer;
use App\Models\FinanceAccount;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransferResource;

class CreateTransfer extends CreateRecord
{
    protected static string $resource = TransferResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        
        $data['registered_by'] = auth()->user()->id;
    
        return $data;
    }
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
     
        
        $original_account = FinanceAccount::findOrFail($data['from_account_id']);
        $target_account = FinanceAccount::findOrFail($data['to_account_id']);
  
        if($original_account->balance < $data['amount']) 
        {
            Notification::make()
                    ->title(trans('main.no_enough_balance'))
                    ->icon('heroicon-o-document-text')
                    ->iconColor('danger')
                    ->send();
            
            $this->halt();
        }
        try{
            DB::beginTransaction();
            $transfer = Transfer::create($data);
            $original_account->balance = $original_account->balance - $data['amount'];
            $original_account->save();
            $target_account->balance = $target_account->balance + $data['amount'];
            $target_account->save();
            Notification::make()
                        ->title(trans('main.transfer_operation_success'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();
            DB::commit();
        }
        catch(\Exception $ex)
        {
            DB::rollBack();
            throw $ex;
        }
    
        return $transfer;
    }
}
