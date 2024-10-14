<?php

namespace App\Filament\Resources\NewestStudentResource\Pages;

use App\Filament\Resources\NewestStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewestStudents extends ListRecords
{
    protected static string $resource = NewestStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
