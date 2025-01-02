<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ExpenseResource;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\EditAction::make(),
        ];
    }
}
