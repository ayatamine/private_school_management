<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use Carbon\Carbon;
use App\Models\File;
use App\Models\User;
use Filament\Actions;
use App\Models\Employee;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\EmployeeResource;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\Action::make('add_files')
            ->color('info')
            ->label(trans('main.add_files'))
            ->closeModalByClickingAway(false)
            ->form([
                FileUpload::make('file')
                    ->label(trans('main.files'))
                    ->directory('employee')
                    ->preserveFileNames(),
                Textarea::make('description')->label(trans('main.description')),
            ])
            ->action(function (array $data,Employee $record) {

                if($data['file'])
                {
                        // foreach($data['files'] as $file)
                        // {
                            //add here extension check if it is image or a file
                            $extension = strtolower(pathinfo($data['file'], PATHINFO_EXTENSION));

                            // Determine the file type based on the extension
                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif','webp','pmp','svg'])) {
                                $fileType = 'image';
                            } elseif (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx','txt','odt','pptx'])) {
                                $fileType = 'document';
                            } 

                            $albumFile = new File();
                            $albumFile->fileable_id = $record->id;
                            $albumFile->fileable_type = Employee::class;
                            $albumFile->type = $fileType; // Set type based on file type
                            $albumFile->file = $data['file'];
                            $albumFile->description = $data['description'];
                            $albumFile->save();
                        // }
                }
                Notification::make()
                ->title(trans('main.file_added_successfully'))
                ->icon('heroicon-o-document-text')
                ->iconColor('success')
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
}
