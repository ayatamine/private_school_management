<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Department;
use App\Models\Designation;
use App\Models\EmploymentDuration;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\EmployeeResource;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
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
}
