<?php

namespace App\Filament\Resources\GeneralFeeResource\Pages;

use App\Filament\Resources\GeneralFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralFees extends ListRecords
{
    protected static string $resource = GeneralFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
