<?php

namespace App\Filament\Resources\AcademicStageResource\Pages;

use App\Filament\Resources\AcademicStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicStage extends EditRecord
{
    protected static string $resource = AcademicStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
