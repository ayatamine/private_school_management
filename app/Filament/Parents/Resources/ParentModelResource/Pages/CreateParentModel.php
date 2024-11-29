<?php

namespace App\Filament\Parents\Resources\ParentModelResource\Pages;

use App\Filament\Parents\Resources\ParentModelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateParentModel extends CreateRecord
{
    protected static string $resource = ParentModelResource::class;
}
