<?php

namespace App\Filament\Parents\Resources\ParentModelResource\Pages;

use App\Filament\Parents\Resources\ParentModelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParentModels extends ListRecords
{
    protected static string $resource = ParentModelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
