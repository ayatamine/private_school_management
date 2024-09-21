<?php

namespace App\Filament\Resources\TuitionFeeResource\Pages;

use App\Filament\Resources\TuitionFeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTuitionFees extends ListRecords
{
    protected static string $resource = TuitionFeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
