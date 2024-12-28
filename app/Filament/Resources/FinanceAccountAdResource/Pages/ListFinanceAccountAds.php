<?php

namespace App\Filament\Resources\FinanceAccountAdResource\Pages;

use App\Filament\Resources\FinanceAccountAdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinanceAccountAds extends ListRecords
{
    protected static string $resource = FinanceAccountAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
