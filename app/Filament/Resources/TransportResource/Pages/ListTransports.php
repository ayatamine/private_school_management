<?php

namespace App\Filament\Resources\TransportResource\Pages;

use App\Filament\Resources\TransportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransports extends ListRecords
{
    protected static string $resource = TransportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
