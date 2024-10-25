<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use Filament\Actions;
use App\Models\PaymentMethod;
use App\Models\FinanceAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\IncomeResource;

class EditIncome extends EditRecord
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try{
            DB::beginTransaction();
       
             $old_value = $record->value;
             $payment = PaymentMethod::findOrFail($data['payment_method_id']);
             $finance_account = FinanceAccount::findOrFail($payment->finance_account_id);

             //return old value back and minus new value
             $finance_account->update([
                'balance'=> $finance_account->balance - $old_value  + $data['value']
             ]);

             DB::commit();
            

        }
        catch(\Exception $ex)
        {
            DB::rollBack();
            Notification::make()
            ->danger()
            ->title('There is something wrong')
            ->body('there is an issue when saving this expense');
            return $this->halt();
        }
        return $record;
    }
}
