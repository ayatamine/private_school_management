<?php

namespace App\Filament\Resources\FinanceAccountResource\Pages;

use App\Filament\Resources\FinanceAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinanceAccounts extends ListRecords
{
    protected static string $resource = FinanceAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
