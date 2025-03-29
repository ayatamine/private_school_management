<?php

namespace App\Filament\Resources\ReceiptVoucherResource\Pages;

use Auth;
use Exception;
use Filament\Actions;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ReceiptVoucherResource;

class CreateReceiptVoucher extends CreateRecord
{
    protected static string $resource = ReceiptVoucherResource::class;
    protected static bool $canCreateAnother = false;
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['registered_by'] =Auth::id();
        $data['status'] ='paid';
        return $data;
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
