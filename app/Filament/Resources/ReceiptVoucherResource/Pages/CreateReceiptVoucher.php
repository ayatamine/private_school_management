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
        $data['registered_by'] =Auth::id();
        $data['status'] ='paid';
        return $data;
    }
}
