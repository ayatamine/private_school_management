<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('printAllExpenses')
                                ->icon('icon-print')
                                ->color('info')
                                ->label(trans('main.print'))
                                ->visible(employeeHasPermission('print_payments_student'))
                                ->url(route('print_pdf',['type'=>"expenses"]))
        ];
    }
}
