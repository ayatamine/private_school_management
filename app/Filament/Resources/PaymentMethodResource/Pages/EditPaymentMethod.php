<?php

namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPaymentMethod extends EditRecord
{
    protected static string $resource = PaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
      
        $data['add_refrence_number']  = $data['is_code_required'] ? true : false;

        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['code']  = '';
        if($data['add_refrence_number'] == false){
           
            $data['is_code_required']  = false;
        }   

        return $data;
    }
}
