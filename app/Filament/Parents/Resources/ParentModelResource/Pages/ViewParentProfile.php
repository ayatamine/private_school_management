<?php

namespace App\Filament\Parents\Resources\ParentModelResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\TextInput;
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
                        
                        TextInput::make(name: 'password')->label(trans('main.password'))
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),
                    ])
                    ->action(function(array $arguments,array $data) {
                        try{
                            DB::beginTransaction();
                            $parent = $this->record;
                            if($data['password'] != "")
                            {
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
