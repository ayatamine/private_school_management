<?php

namespace App\Filament\Resources\UpgradeStudentResource\Pages;

use App\Filament\Resources\UpgradeStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUpgradeStudent extends EditRecord
{
    protected static string $resource = UpgradeStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
