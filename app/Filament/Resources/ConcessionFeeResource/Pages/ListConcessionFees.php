<?php

namespace App\Filament\Resources\ConcessionFeeResource\Pages;

use App\Filament\Resources\ConcessionFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConcessionFees extends ListRecords
{
    protected static string $resource = ConcessionFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
