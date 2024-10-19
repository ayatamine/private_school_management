<?php

namespace App\Filament\Resources\TuitionFeeReportsResource\Pages;

use Filament\Actions;
use App\Models\TuitionFee;
use Filament\Actions\Action;
use App\Models\ConcessionFee;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Filament\Resources\TuitionFeeReportsResource;

class ViewTuitionFeeReport extends ViewRecord
{
    use InteractsWithActions;
    protected static string $resource = TuitionFeeReportsResource::class;
    
    public function editPartitions(): Action
    {
        return Action::make('editPartitions')
        ->label(trans_choice('main.edit_partition',1))
        ->form([
             //TODO: here you should put concession of the current year
                  Select::make('concession_fee_id')
                                ->label(trans_choice('main.concession_fee',1))
                                ->options(ConcessionFee::active()->pluck('name','id'))
                                ->required(),
        ])
        ->action(function (array $arguments,array $data) {
            $concession_fee = ConcessionFee::findOrFail($data['concession_fee_id']);
            $fee =TuitionFee::findOrFail($arguments['fee_id']);
            $payment_partition = $fee->payment_partition;
            if(array_key_exists($arguments['partition'],$payment_partition))
            {
               
                $discounts=$payment_partition;
                // foreach($payment_partition as $key=>$value)
                // {
                //     $discounts[$key] = $value;
                // }
                $discounts[0]['discount_type'] = $concession_fee->type;
                $discounts[0]['discount_value'] = $concession_fee->value;

                DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ?',[json_encode($discounts),$arguments['fee_id'],$arguments['feeable_type']]);
            }
            Notification::make()
            ->title(trans('main.partition_updated_successfully'))
            ->icon('heroicon-o-document-text')
            ->iconColor('success')
            ->send();
            return redirect(request()->header('Referer'));
            // dd($fee);
            // DB::update('update student_fee set name = ? where id = ?',[$name,$arguments['fee_id']]);
            
        });
    }
}
