<?php

use App\Models\File;
use Filament\Actions;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;

if(!function_exists('employeeHasPermission'))
{
     function employeeHasPermission($permission):bool
    { 
        return  (auth()->user()->hasRole('super_admin')) || (auth()->user()?->employee && auth()->user()?->employee->hasPermissionTo($permission));
    }
}
if(!function_exists('addFile'))
{
     function addFile($record,$directory):Action
    { 
        return Actions\Action::make('add_files')
        ->color('info')
        ->label(trans('main.add_files'))
        ->closeModalByClickingAway(false)
        ->form([
            FileUpload::make('file')
                ->label(trans('main.files'))
                ->directory($directory)
                ->preserveFileNames(),
            Textarea::make('description')->label(trans('main.description')),
        ])
        ->action(function (array $data,$record) {

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
                        else {
                            Notification::make()
                            ->title(trans('main.file_type_is_not_supported'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('danger')
                            ->send();
                            return; 
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
        });
    }
}
if(!function_exists('print_table_resrource_list'))
{
    function print_table_resrource_list($permission_name,$route):Action
    {
        return  Actions\Action::make('print_table')
        ->icon('icon-print')
        ->color('info')
        ->label(trans('main.print'))
        ->visible(employeeHasPermission($permission_name))
        ->url($route);
    }
}