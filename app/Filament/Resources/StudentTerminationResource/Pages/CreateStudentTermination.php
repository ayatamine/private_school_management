<?php

namespace App\Filament\Resources\StudentTerminationResource\Pages;

use Filament\Actions;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StudentTerminationResource;

class CreateStudentTermination extends CreateRecord
{
    protected static string $resource = StudentTerminationResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        
   
        $data['terminated_by'] = Auth::id();
        $student = Student::findOrFail($data['student_id'])->update($data);
        $this->halt();
        return $data;
        
    }

}
