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
            Action::make('show_attachment')
            ->color('primary')
            ->label(trans('main.show_attachment'))
            ->visible($this->record?->attachment != null)
            ->url(asset('storage/'.$this->record?->attachment))
            ->openUrlInNewTab()
        ];
    }
}
