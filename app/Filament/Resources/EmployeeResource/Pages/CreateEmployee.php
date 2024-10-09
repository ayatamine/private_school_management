<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EmployeeResource;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
     
        if($data['new_employee'] == true)
        {
            unset($data['age']);
            $user = User::create([
                'national_id' =>$data['national_id'],
                'gender' =>$data['gender'],
                'phone_number' =>$data['phone_number'],
                'email' =>$data['email'],
                'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
            ]);
            $data['user_id'] = $user?->id;
            $data['joining_date'] = now();
            $data['nationality'] = $data['nationality'] =="saudian" ? $data['nationality'] : $data['nationality2'];

        return $data;
        }
        else
        {
            try{
                unset($data['age']);
                DB::beginTransaction();
                $employee = Employee::findOrFail(intval($data['registration_number']));
                //update user related
                $employee->user->update([
                    'national_id' =>$data['national_id'],
                    'gender' =>$data['gender'],
                    'phone_number' =>$data['phone_number'],
                    'email' =>$data['email'],
                    'password' => isset($data['password']) ? bcrypt($data['password']) :bcrypt('123456')
                ]);
                foreach (['national_id','gender','phone_number','email','password'] as $key => $value) {
                    unset($data[$value]);
                }
                $employee->update($data);
               
                $data = $employee->toArray();
                Notification::make()
                            ->title(trans('main.employee_registered_successfully'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
                DB::commit();
            }
            catch(\Exception $ex)
            {
                DB::rollBack();
                        Notification::make()
                            ->title($ex->getMessage())
                            ->icon('heroicon-o-document-text')
                            ->iconColor('danger')
                            ->send();
            }
            $this->halt();
            return [];
        }

    }
}
