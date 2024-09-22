<?php

namespace App\Filament\Resources\GeneralFeeResource\Pages;

use App\Filament\Resources\GeneralFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralFee extends EditRecord
{
    protected static string $resource = GeneralFeeResource::class;

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
