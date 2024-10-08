<?php

namespace App\Filament\Resources\SemesterResource\Pages;

use Filament\Actions;
use App\Models\Course;
use App\Models\AcademicStage;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SemesterResource;

class EditSemester extends EditRecord
{
    protected static string $resource = SemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['academic_stage_id'] = Course::find($data['course_id'])->academic_stage_id;
        
        return $data;
    }
}
