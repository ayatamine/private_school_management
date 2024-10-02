<?php

namespace App\Filament\Resources\SchoolSettingResource\Pages;

use App\Filament\Resources\SchoolSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchoolSetting extends EditRecord
{
    protected static string $resource = SchoolSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
