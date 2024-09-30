<?php

namespace App\Filament\Resources\ReceiptVoucherResource\Pages;

use App\Filament\Resources\ReceiptVoucherResource;
use Auth;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['registered_by'] =Auth::id();
        $data['is_approved'] =Auth::id();
        return $data;
    }
}
