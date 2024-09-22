<?php

namespace App\Filament\Resources\TransportFeeResource\Pages;

use App\Filament\Resources\TransportFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransportFee extends CreateRecord
{
    protected static string $resource = TransportFeeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        $data['payment_partition_count'] = count($data['payment_partition']);
        return $data;
    }
}
