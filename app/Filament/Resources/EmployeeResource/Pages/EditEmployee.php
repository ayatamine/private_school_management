<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Actions;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use App\Filament\Resources\EmployeeResource;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('approve_employee')
            ->visible(fn(Employee $record)=>$record->contract_end_date == null)
                ->label(trans('main.approve_employee'))
                ->color('success')
                ->requiresConfirmation()
                ->action(function(Employee $record){

                    Notification::make()
                        ->title(trans('main.employee_data_not_completed'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('danger')
                        ->send();
                }),
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

        try{
            unset($data['age']);
            DB::beginTransaction();
            $employee = $this->record;
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

                // Ensure all permissions exist in the database
                foreach ($flattenedPermissions as $permission) {
                    $permission = Permission::findOrCreate($permission);

                    $employee->givePermissionTo($permission);
                }
                // Sync the permissions for the user
                $employee->syncPermissions($flattenedPermissions);
                
                
            $employee->update($data);
           
            // $data = $employee->toArray();
            Notification::make()
                        ->title(trans('main.employee_updated_successfully'))
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
