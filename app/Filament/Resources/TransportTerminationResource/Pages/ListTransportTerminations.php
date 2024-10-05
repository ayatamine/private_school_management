<?php

namespace App\Filament\Resources\TransportTerminationResource\Pages;

use App\Filament\Resources\TransportTerminationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransportTerminations extends ListRecords
{
    protected static string $resource = TransportTerminationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
}
