<?php

namespace App\Filament\Resources\TuitionFeeReportsResource\Pages;

use App\Filament\Resources\TuitionFeeReportsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTuitionFeeReports extends EditRecord
{
    protected static string $resource = TuitionFeeReportsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
