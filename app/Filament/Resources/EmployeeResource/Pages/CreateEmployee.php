<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Spatie\Permission\Models\Permission;
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
    protected function handleRecordCreation(array $data): Model
    {
        try{
 
            DB::beginTransaction();
            $data['nationality'] = $data['nationality'] =="saudian" ? $data['nationality'] : $data['nationality2'];
            
            
            //filter data to left only permissions
                                                        
            $required_fields = ['first_name', 'middle_name', 'third_name', 'last_name', 'gender','nationality'
            , 'gender','email','phone_number','national_id','identity_type','identity_expire_date','birth_date',
            'birth_date','age','social_status','study_degree','study_speciality','national_address','iban'
            ];

            $permissions = array_filter($data, function ($key) use ($required_fields) {
                return !in_array($key, $required_fields);
            }, ARRAY_FILTER_USE_KEY);

            $data = array_filter($data, function ($key) use ($required_fields) {
                return in_array($key, $required_fields);
            }, ARRAY_FILTER_USE_KEY);
            // Flatten the nested permissions array
            $flattenedPermissions = collect($permissions)->flatten()->toArray();

            //create new employee
            $employee = Employee::create($data);
            // Ensure all permissions exist in the database
            foreach ($flattenedPermissions as $permission) {
                $permission = Permission::findOrCreate($permission);

                $employee->givePermissionTo($permission);
            }
            // Sync the permissions for the user
            $employee->syncPermissions($flattenedPermissions);
    
            
        
            
            DB::commit();
            return $employee;
        }
        catch(\Exception $ex)
        {
            DB::rollBack();
            Notification::make('')
            ->message($ex->getMessage())
            ->color('danger')
            ->send();
        }
    }
}
