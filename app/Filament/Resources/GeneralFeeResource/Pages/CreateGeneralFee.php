<?php

namespace App\Filament\Resources\GeneralFeeResource\Pages;

use App\Filament\Resources\GeneralFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGeneralFee extends CreateRecord
{
    protected static string $resource = GeneralFeeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        $data['payment_partition_count'] = count($data['payment_partition']);
        return $data;
    }
}
