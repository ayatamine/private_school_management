<?php

namespace App\Filament\Resources\TuitionFeeResource\Pages;

use App\Filament\Resources\TuitionFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTuitionFee extends CreateRecord
{
    protected static string $resource = TuitionFeeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        $data['payment_partition_count'] = count($data['payment_partition']);
        return $data;
    }
}
