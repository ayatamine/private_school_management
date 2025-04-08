<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use Filament\Actions;
use App\Models\Expense;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ExpenseResource;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['value'] = floatval(str_replace(',', '', $data['value']));
        return $data;
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cancel')
            ->label(fn(Expense $record )=> $record->is_cancelled == true ?  trans('main.activate') :  trans('main.cancel')  )
            ->color(fn(Expense $record )=> $record->is_cancelled == true ? "success" : "danger"  )
            ->requiresConfirmation()  
            ->form([
                \Filament\Forms\Components\TextInput::make('cancel_reason')
                ->visible(fn(Expense $record )=> $record->is_cancelled == false)
                ->label(trans('main.cancel_reason')),
            ])              
            ->action(function(Expense $expense,array $data): void {
               
                 $expense->is_cancelled = !$expense->is_cancelled;
                 $expense->cancel_reason = isset($data['cancel_reason']) ? $data['cancel_reason'] : null;
                 $expense->save();
            }),
            Actions\DeleteAction::make(),
            Actions\EditAction::make(),
        ];
    }
}
