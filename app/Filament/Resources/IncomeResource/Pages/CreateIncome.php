<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        
        $data['registered_by'] = auth()->user()->id;
    
        return $data;
    }
}
