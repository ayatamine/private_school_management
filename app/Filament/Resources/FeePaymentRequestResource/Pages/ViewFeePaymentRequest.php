<?php

namespace App\Filament\Resources\FeePaymentRequestResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Models\ReceiptVoucher;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\FeePaymentRequestResource;

class ViewFeePaymentRequest extends ViewRecord
{
    protected static string $resource = FeePaymentRequestResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->color('info'),
            Action::make('print_receipt_voucher')
            ->icon('icon-print')
            ->color('info')
            ->label(trans('main.print_receipt_voucher'))
            ->url(fn(ReceiptVoucher $record) => route('print_pdf',['type'=>"receipt_voucher",'id'=>$record->id])),
            Action::make('show_attachment')
            ->color('primary')
            ->label(trans('main.show_attachment'))
            ->visible($this->record?->document != null)
            ->openUrlInNewTab(true)
            ->url(asset('storage/'.$this->record?->document)),
            Action::make('reject_payment_request')
            ->color('danger')
            ->visible(fn(ReceiptVoucher $receiptVoucher) =>$receiptVoucher->status == "pending")
            ->label(trans('main.reject_payment_request'))
            ->form([
                Textarea::make('reject_note')->label(trans('main.enter_reject_reason')),
            ])
            ->action(function(array $data,ReceiptVoucher $record) {
                $record->status ='rejected';
                $record->reject_note =$data['reject_note'];
                $record->save();
                Notification::make()
                            ->title(trans('main.reject_payment_request_successfully'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
            }),
            Action::make('approve_payment_request')
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn(ReceiptVoucher $receiptVoucher) =>$receiptVoucher->status == "pending")
            ->label(trans('main.approve_payment_request'))
            ->action(function(ReceiptVoucher $record) {
                $record->status ='paid';
                $record->save();
                Notification::make()
                            ->title(trans('main.approve_payment_request_successfully'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
            })
        ];
    }
}
