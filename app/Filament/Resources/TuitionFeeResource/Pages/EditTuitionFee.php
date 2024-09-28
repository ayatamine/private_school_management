<?php

namespace App\Filament\Resources\TuitionFeeResource\Pages;

use App\Filament\Resources\TuitionFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTuitionFee extends EditRecord
{
    protected static string $resource = TuitionFeeResource::class;

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