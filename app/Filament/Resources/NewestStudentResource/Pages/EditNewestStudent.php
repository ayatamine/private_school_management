<?php

namespace App\Filament\Resources\NewestStudentResource\Pages;

use App\Filament\Resources\NewestStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewestStudent extends EditRecord
{
    protected static string $resource = NewestStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
