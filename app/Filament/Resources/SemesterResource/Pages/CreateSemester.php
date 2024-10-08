<?php

namespace App\Filament\Resources\SemesterResource\Pages;

use App\Filament\Resources\SemesterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSemester extends CreateRecord
{
    protected static string $resource = SemesterResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['academic_stage_id']);
        return $data;
    }
}
