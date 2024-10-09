<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EmployeeResource;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;

        $data['age'] = (new Carbon($data['birth_date']))->diffInYears(Carbon::now())." ".trans_choice('main.year',2);

        
        if($data['nationality'] == 'saudian')
        {
            $data['nationality2'] = "";            
        }else
        {
            $data['nationality2'] = $data['nationality']; 
            $data['nationality'] = "other"; 
        }

        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
dd($data);
            // unset($data['age']);
  
            // User::findOrFail($this->record->user_id)->update([
            //     'national_id' =>$data['national_id'],
            //     'gender' =>$data['gender'],
            //     'phone_number' =>$data['phone_number'],
            //     'email' =>$data['email'],
            //     'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
            // ]);
           
            $data['nationality'] = $data['nationality'] =="saudian" ? $data['nationality'] : $data['nationality2'];

        return $data;

    }
}
