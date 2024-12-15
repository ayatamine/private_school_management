<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // dd($data);
        // $required_fields = ['username', 'national_id', 'email', 'phone_number', 'password', 'gender'];

        // $permissions = array_filter($data, function ($key) use ($required_fields) {
        //     return !in_array($key, $required_fields);
        // }, ARRAY_FILTER_USE_KEY);
        // $data = array_filter($data, function ($key) use ($required_fields) {
        //     return in_array($key, $required_fields);
        // }, ARRAY_FILTER_USE_KEY);
        // // Flatten the nested permissions array
        // $flattenedPermissions = collect($permissions)->flatten()->toArray();

        // // Ensure all permissions exist in the database
        // foreach ($flattenedPermissions as $permission) {
        //     Permission::findOrCreate($permission);
        // }

        // // Sync the permissions for the user
        // auth()->user()->syncPermissions($flattenedPermissions);
        if(isset($data['password'])) $data['password'] = bcrypt($data['password']);

        return $data;
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try{
 
            DB::beginTransaction();
            $required_fields = ['username', 'national_id', 'email', 'phone_number', 'password', 'gender'];

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

                $record->givePermissionTo($permission);
            }
            // Sync the permissions for the user
            $record->syncPermissions($flattenedPermissions);
            if($data['password'] == null) $data['password'] = $this->record->password;

    
            $record->update($data);
        
            
            DB::commit();
            return $record;
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
