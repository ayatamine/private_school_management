<?php

namespace App\Filament\Resources\NewestStudentResource\Pages;

use App\Filament\Resources\NewestStudentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNewestStudent extends CreateRecord
{
    protected static string $resource = NewestStudentResource::class;
}
