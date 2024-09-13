<?php

namespace App\Filament\Resources\AcademicStageResource\Pages;

use App\Filament\Resources\AcademicStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicStages extends ListRecords
{
    protected static string $resource = AcademicStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
