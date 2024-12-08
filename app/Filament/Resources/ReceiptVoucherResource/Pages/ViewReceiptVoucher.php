<?php

namespace App\Filament\Resources\ReceiptVoucherResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ReceiptVoucherResource;

class ViewReceiptVoucher extends ViewRecord
{
    protected static string $resource = ReceiptVoucherResource::class;
    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
            Action::make('show_attachment')
            ->color('primary')
            ->label(trans('main.show_attachment'))
            ->visible($this->record?->document != null)
            ->openUrlInNewTab(true)
            ->url(asset('storage/'.$this->record?->document)),
        ];
    }
}
