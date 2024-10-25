<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ExpenseResource;
use App\Models\FinanceAccount;
use App\Models\PaymentMethod;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        
        $data['registered_by'] = auth()->user()->id;
    
        return $data;
    }
    protected function handleRecordCreation(array $data): Model
    {
        try{
            DB::beginTransaction();
             $created =  static::getModel()::create($data);
             $payment = PaymentMethod::findOrFail($data['payment_method_id']);
             $finance_account = FinanceAccount::findOrFail($payment->finance_account_id);
             $finance_account->update([
                'balance'=> $finance_account->balance - $data['value']
             ]);
             DB::commit();
             return $created;

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
    }
}
