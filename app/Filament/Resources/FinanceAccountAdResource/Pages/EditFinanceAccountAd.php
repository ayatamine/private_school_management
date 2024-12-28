<?php

namespace App\Filament\Resources\FinanceAccountAdResource\Pages;

use App\Filament\Resources\FinanceAccountAdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinanceAccountAd extends EditRecord
{
    protected static string $resource = FinanceAccountAdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
