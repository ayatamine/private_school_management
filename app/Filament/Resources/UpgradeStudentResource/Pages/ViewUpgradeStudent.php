<?php

namespace App\Filament\Resources\UpgradeStudentResource\Pages;

use MPDF;
use Filament\Forms;
use App\Models\User;
use Filament\Actions;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Semester;
use App\Models\TuitionFee;
use App\Models\ParentModel;
use App\Models\AcademicYear;
use Filament\Actions\Action;
use App\Models\AcademicStage;
use App\Models\ConcessionFee;
use App\Models\SchoolSetting;
use App\Models\ReceiptVoucher;
use Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Traits\InteractWithStudentRecord;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\StudentResource;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\ReceiptVoucherResource;
use Filament\Actions\Concerns\InteractsWithActions;

class ViewUpgradeStudent extends ViewRecord  implements  HasActions,HasForms
{
    use InteractsWithActions; use InteractsWithForms;use InteractWithStudentRecord;
    protected static string $resource = StudentResource::class;
    protected static string $view = 'filament.resources.students.pages.view-student';
    protected function getHeaderActions(): array
    {
        return [
 
                    
        ];
    }
    public function getFormStatePath(): string
    {
        return 'form';
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = User::findOrFail($data['user_id']);
        $data['national_id'] = $user->national_id;
        $data['gender'] = $user->gender;
        $data['phone_number'] = $user->phone_number;
        $data['email'] = $user->email;

        $parent = ParentModel::find($data['parent_id']);
        // $data['parent_relation']  = '$parent?->parent_relation';
        $data['parent_national_id']  = $parent?->parent_national_id;
        $data['parent_email']  = $parent?->parent_email;
        $data['parent_phone_number']  = $parent?->parent_phone_number;
        $data['parent_gender']  = $parent?->parent_gender;

        return $data;
    }
   
    
    
}
