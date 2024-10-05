<?php

namespace App\Filament\Resources\TransportTerminationResource\Pages;

use Filament\Actions;
use App\Models\Student;
use App\Models\Transport;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransportTerminationResource;

class CreateTransportTermination extends CreateRecord
{
    protected static string $resource = TransportTerminationResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
        $data['terminated_by'] = Auth::id();
        $student = Transport::whereStudentId($data['student_id'])->first()->update($data);
        Notification::make()
                            ->title(trans('main.student_termination_success'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
        $this->halt();
        return $data;
        
    }
    public function getBreadcrumb(): string
    {
        return '';
    }
}
