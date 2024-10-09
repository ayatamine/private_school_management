<?php

namespace App\Filament\Resources\FeePaymentRequestResource\Pages;

use App\Filament\Resources\FeePaymentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeePaymentRequest extends EditRecord
{
    protected static string $resource = FeePaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
