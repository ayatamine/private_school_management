<?php

namespace App\Filament\Resources\StudentTerminationResource\Pages;

use App\Filament\Resources\StudentTerminationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentTerminations extends ListRecords
{
    protected static string $resource = StudentTerminationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
