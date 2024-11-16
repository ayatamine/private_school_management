<?php

namespace App\Filament\Resources\GeneralFeeResource\Pages;

use Filament\Actions;
use App\Models\Student;
use App\Models\GeneralFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\GeneralFeeResource;

class CreateGeneralFee extends CreateRecord
{
    protected static string $resource = GeneralFeeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        $data['payment_partition_count'] = count($data['payment_partition']);
        return $data;
    }
    protected function handleRecordCreation(array $data): Model
    {
        try{
            DB::beginTransaction();
            $record = static::getModel()::create($data);
          
            // add other fees
            $students = Student::whereHas('semester',function($query) use ($data){
                $query->whereCourseId($data['course_id']);
            })->whereStatus('approved')->get();

            foreach ($students as  $student) {
                $student->otherFees()->attach($record->id);
                // add concession fees
                            
            $discounts = $record->payment_partition;
                            
            $discounts[0]['discount_type'] = "percentage";
            $discounts[0]['discount_value'] = 0;

            DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',
            [json_encode($discounts),$record->id,GeneralFee::class,$student->id]);
            }
        
            
          DB::commit();
        }
        catch(\Exception $ex)
        {
            Notification::make()
                        ->title(trans('main.error'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('danger')
                        ->send();
            DB::rollBack();
        }
        return $record;
    }
}
