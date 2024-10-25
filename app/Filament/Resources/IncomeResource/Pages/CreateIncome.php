<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use Filament\Actions;
use App\Models\PaymentMethod;
use App\Models\FinanceAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use App\Filament\Resources\IncomeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;
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
                'balance'=> $finance_account->balance + $data['value']
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
