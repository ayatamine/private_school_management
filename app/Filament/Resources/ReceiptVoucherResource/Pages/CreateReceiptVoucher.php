<?php

namespace App\Filament\Resources\ReceiptVoucherResource\Pages;

use Auth;
use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ReceiptVoucherResource;
use App\Models\PaymentMethod;
use Exception;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;
    public function mutateFormDataBeforeCreate(array $data): array
    {
       try
       {
        DB::beginTransaction();
        $data['registered_by'] =Auth::id();
        $data['status'] ='paid';

        $payment_method = PaymentMethod::findOrFail($data['payment_method_id']);
        $payment_method->financeAccount->update(['balance'=>$payment_method->financeAccount + floatval($data['value'])]);
        DB::commit();
        return $data;
       }
       catch(Exception $ex)
       {
        throw $ex;
       }
    }
}
