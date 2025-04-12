<?php

namespace App\Filament\Resources\TransportResource\Pages;

use Filament\Actions;
use App\Models\Transport;
use App\Models\TransportFee;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransportResource;

class CreateTransport extends CreateRecord
{
    protected static string $resource = TransportResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
            $data['registered_by'] = Auth::id();
            // $transport_fee = TransportFee::findOrFail($data['transport_fee_id']);
      
            return $data;
    }
    // protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    // {  
    //     foreach($data['transport_fees'] as $fee_id)
    //     {
    //         // $transport_fee = TransportFee::findOrFail($fee_id);
    //         $data['transport_fee_id'] = $fee_id;
          
    //         $transport = Transport::create($data);
    //     }
    //     return $this->halt();
    // }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
