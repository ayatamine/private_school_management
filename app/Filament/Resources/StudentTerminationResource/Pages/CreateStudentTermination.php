<?php

namespace App\Filament\Resources\StudentTerminationResource\Pages;

use Filament\Actions;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StudentTerminationResource;

class CreateStudentTermination extends CreateRecord
{
    protected static string $resource = StudentTerminationResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
        if(Student::findOrFail($data['student_id'])->total_fees_rest != 0 && auth()->user()->can('terminate_student_private'))
        {
            Notification::make()
                ->title(trans('main.student_termination_balance_error'))
                ->icon('heroicon-o-document-text')
                ->iconColor('danger')
                ->send();
            $this->halt();
            return $data;
        }
        $data['terminated_by'] = Auth::id();
        $data['semester_id'] = null;
        $data['is_approved'] = false;
        $student = Student::findOrFail($data['student_id'])->update($data);
        Notification::make()
                            ->title(trans('main.student_termination_success'))
                            ->icon('heroicon-o-document-text')
                            ->iconColor('success')
                            ->send();
        $this->halt();
        return $data;
        
    }

}
