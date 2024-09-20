<?php

namespace App\Filament\Resources\ValueAddedTaxResource\Pages;

use App\Filament\Resources\ValueAddedTaxResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListValueAddedTaxes extends ListRecords
{
    protected static string $resource = ValueAddedTaxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
