<?php

namespace App\Filament\Student\Resources\ReceiptVoucherResource\Pages;

use App\Filament\Student\Resources\ReceiptVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['client_id'] = auth()->user()?->student?->id;
        dd($data);
        return $data;
    }
}
