<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use DB;
use Filament\Actions;
use Filament\Actions\Action;
use App\Models\PaymentMethod;
use App\Models\FinanceAccount;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ExpenseResource;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Action::make('show_attachment')
            ->color('primary')
            ->label(trans('main.show_attachment'))
            ->visible($this->record?->attachment != null)
            ->url(asset('storage/'.$this->record?->attachment))
            ->openUrlInNewTab()
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['value'] = floatval(str_replace(',', '', $data['value']));
        return $data;
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try{
            DB::beginTransaction();
            
             $old_value =floatVal($record->value);
             $payment = PaymentMethod::findOrFail($data['payment_method_id']);
             $finance_account = FinanceAccount::findOrFail($payment->finance_account_id);

             //TODO::you should check if payment method changed so the related account may change
             //return old value back and minus new value
             $finance_account->update([
                'balance'=> $finance_account->balance + $old_value  - $data['value']
             ]);
             if($record->attchment)
             {
                $data['attachment'] = Storage::putFile('expenses',$data['attachment']);
             } 
       
             $record->update($data);
             DB::commit();
            

        }
        catch(\Exception $ex)
        {
            dd($ex);
            DB::rollBack();
            Notification::make()
            ->danger()
            ->title('There is something wrong')
            ->body('there is an issue when saving this expense');
            return $this->halt();
        }
        return $record;
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
