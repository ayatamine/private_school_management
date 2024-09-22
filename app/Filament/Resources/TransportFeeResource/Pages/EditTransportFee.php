<?php

namespace App\Filament\Resources\TransportFeeResource\Pages;

use App\Filament\Resources\TransportFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransportFee extends EditRecord
{
    protected static string $resource = TransportFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
     
        $data['payment_partition_count'] = count($data['payment_partition']);
        return $data;
    }
}
