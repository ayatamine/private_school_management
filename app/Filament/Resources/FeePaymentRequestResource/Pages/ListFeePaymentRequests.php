<?php

namespace App\Filament\Resources\FeePaymentRequestResource\Pages;

use App\Filament\Resources\FeePaymentRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeePaymentRequests extends ListRecords
{
    protected static string $resource = FeePaymentRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
