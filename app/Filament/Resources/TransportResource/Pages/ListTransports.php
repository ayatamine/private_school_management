<?php

namespace App\Filament\Resources\TransportResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TransportResource;
use App\Filament\Resources\TransportTerminationResource;

class ListTransports extends ListRecords
{
    protected static string $resource = TransportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->visible(employeeHasPermission('create_transport_registeration_transport'))  
             ,
            Actions\Action::make('create')
            ->label(trans('main.transport_termination'))
            ->visible(employeeHasPermission('terminate_transport_registeration_transport'))
            ->color('danger')
            ->url(TransportTerminationResource::getUrl('create')),
        ];
    }
}
