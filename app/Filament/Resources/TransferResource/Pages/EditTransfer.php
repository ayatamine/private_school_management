<?php

namespace App\Filament\Resources\TransferResource\Pages;

use Filament\Actions;
use App\Models\FinanceAccount;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TransferResource;

class EditTransfer extends EditRecord
{
    protected static string $resource = TransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
     
        
        $original_account = FinanceAccount::findOrFail($data['from_account_id']);
        $target_account = FinanceAccount::findOrFail($data['to_account_id']);
        
        //return old value back and minus new value
        if(($original_account->balance +$record->amount) < $data['amount']) 
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
            $original_account->balance+=$record->amount;
            $original_account->save();
            $target_account->balance-=$record->amount;
            $target_account->save();

            $record->update($data);
            $original_account->balance = $original_account->balance - $data['amount'];
            $original_account->save();
            $target_account->balance = $target_account->balance + $data['amount'];
            $target_account->save();
            Notification::make()
                        ->title(trans('main.transfer_operation_update_success'))
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
     return $record;
    }
}
