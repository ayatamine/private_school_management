<?php

namespace App\Filament\Resources\FinanceAccountResource\Pages;

use App\Filament\Resources\FinanceAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinanceAccount extends CreateRecord
{
    protected static string $resource = FinanceAccountResource::class;
}