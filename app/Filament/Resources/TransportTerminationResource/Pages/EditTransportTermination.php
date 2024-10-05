<?php

namespace App\Filament\Resources\TransportTerminationResource\Pages;

use App\Filament\Resources\TransportTerminationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransportTermination extends EditRecord
{
    protected static string $resource = TransportTerminationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
            // $data['student_id'] = $this->record->student->id;

        return $data;
    }
}
