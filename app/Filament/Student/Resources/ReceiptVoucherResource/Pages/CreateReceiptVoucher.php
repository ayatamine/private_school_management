<?php

namespace App\Filament\Student\Resources\ReceiptVoucherResource\Pages;

use App\Filament\Student\Resources\ReceiptVoucherResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;
    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['student_id'] = auth()->user()?->student?->id;
        $data['registered_by'] = auth()->user()?->id;
        $data['added_by'] = "student";
        return $data;
    }
}
