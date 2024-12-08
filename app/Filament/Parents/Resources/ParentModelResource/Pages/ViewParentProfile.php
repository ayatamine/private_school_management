<?php

namespace App\Filament\Parents\Resources\ParentModelResource\Pages;

use Filament\Forms;
use App\Models\User;
use Filament\Actions;
use App\Models\ParentModel;
use Filament\Actions\Action;
use Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Parents\Resources\ParentModelResource;

class ViewParentProfile extends ViewRecord
{
    protected static string $resource = ParentModelResource::class;
    protected function getHeaderActions(): array
    {
        return [

            Action::make('change_password')
                    ->color('warning')
                    ->label(trans('main.change_password'))
                    ->form([
                        
                        Forms\Components\TextInput::make(name: 'old_password')->label(trans('main.old_password'))
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make(name: 'password')->label(trans('main.new_password'))
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),
                    ])
                    ->action(function(array $arguments,array $data) {
                        try{
                            DB::beginTransaction();
                            $parent = ParentModel::findOrFail($this->record->id);
                            if($data['password'] != "")
                            {
                                //check old password 
                                if (!Hash::check($data['old_password'],$parent?->user?->password)) {
                                    Notification::make()
                                    ->title(trans('main.current_password_wrong'))
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('danger')
                                    ->send();
                                    return ;
                                }
                                
                              $parent->user->update([
                                    'password' =>  bcrypt($data['password'])
                             ]);  
                            }
                            
                            DB::commit();
                            Notification::make()
                                                ->title(trans('main.password_updated_successfully'))
                                                ->icon('heroicon-o-document-text')
                                                ->iconColor('success')
                                                ->send();
                        }
                        catch(\Exception $ex)
                        {
                            DB::rollBack();
                            Notification::make()
                            ->title($ex)
                            ->icon('heroicon-o-document-text')
                            ->iconColor('danger')
                            ->send();
                        }
                        
                    

                    }),
            
                    
        ];
    }
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
