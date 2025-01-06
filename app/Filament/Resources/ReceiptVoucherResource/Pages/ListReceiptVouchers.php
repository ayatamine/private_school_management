<?php

namespace App\Filament\Resources\ReceiptVoucherResource\Pages;

use App\Filament\Resources\ReceiptVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReceiptVouchers extends ListRecords
{
    protected static string $resource = ReceiptVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            print_table_resrource_list('print_receipt::voucher',route('print_pdf',['type'=>"receipt_voucher_list"]))
        ];
    }
}
