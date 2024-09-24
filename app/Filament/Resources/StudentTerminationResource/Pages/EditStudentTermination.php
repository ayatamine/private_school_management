<?php

namespace App\Filament\Resources\StudentTerminationResource\Pages;

use App\Filament\Resources\StudentTerminationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentTermination extends EditRecord
{
    protected static string $resource = StudentTerminationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
            $data['student_id'] = $this->record->id;

        return $data;
    }
}
