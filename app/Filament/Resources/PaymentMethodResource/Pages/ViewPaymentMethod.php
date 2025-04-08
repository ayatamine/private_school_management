<?php

namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentMethod extends ViewRecord
{
    protected static string $resource = PaymentMethodResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make()->visible(function($record){
                return $record->expenses()->count() == 0 && $record->incomes()->count() == 0;
            }),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
      
        $data['add_refrence_number']  = $data['is_code_required'] ? true : false;

        return $data;
    }
}
