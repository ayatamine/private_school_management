<?php

namespace App\Filament\Parents\Resources\ParentModelResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Parents\Resources\ParentModelResource;

class ViewParentProfile extends ViewRecord
{
    protected static string $resource = ParentModelResource::class;
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        // $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;

        return $data;
    }
}
