<?php

namespace App\Filament\Resources\ValueAddedTaxResource\Pages;

use App\Filament\Resources\ValueAddedTaxResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditValueAddedTax extends EditRecord
{
    protected static string $resource = ValueAddedTaxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
