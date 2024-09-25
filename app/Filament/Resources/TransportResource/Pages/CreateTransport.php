<?php

namespace App\Filament\Resources\TransportResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransportResource;

class CreateTransport extends CreateRecord
{
    protected static string $resource = TransportResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
            $data['registered_by'] = Auth::id();

            return $data;
    }
}
