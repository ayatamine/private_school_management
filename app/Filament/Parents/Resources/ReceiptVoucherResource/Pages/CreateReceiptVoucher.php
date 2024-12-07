<?php

namespace App\Filament\Parents\Resources\ReceiptVoucherResource\Pages;

use App\Filament\Parents\Resources\ReceiptVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;
    protected static bool $canCreateAnother = false;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['registered_by'] = auth()->user()?->id;
        $data['added_by'] = "parent";
        return $data;
    }
}
