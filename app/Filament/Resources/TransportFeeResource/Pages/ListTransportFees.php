<?php

namespace App\Filament\Resources\TransportFeeResource\Pages;

use App\Filament\Resources\TransportFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransportFees extends ListRecords
{
    protected static string $resource = TransportFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
