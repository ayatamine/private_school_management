<?php

namespace App\Filament\Parents\Resources\ReceiptVoucherResource\Pages;

use App\Filament\Parents\Resources\ReceiptVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceiptVoucher extends EditRecord
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
