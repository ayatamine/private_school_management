<?php

namespace App\Filament\Resources\UpgradeStudentResource\Pages;

use Filament\Actions;
use App\Models\Course;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Semester;
use App\Models\AcademicYear;
use Filament\Actions\Action;
use App\Models\AcademicStage;
use App\Models\StudentSemester;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\UpgradeStudentResource;

class ListUpgradeStudents extends ListRecords
{
    protected static string $resource = UpgradeStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upgrade_student')
            ->color('success')
            ->label(trans('main.upgrade_semester_students'))
            ->visible(employeeHasPermission('upgrade_student_student'))
            ->form([
                Select::make('current_semester_id')->label(trans('main.currentSemester'))->required()
                    ->options(fn (Get $get): Collection => Semester::query()
                    ->pluck('name', 'id'))->live(),
               
                Section::make(trans('main.new_semester'))
                ->schema([
                    Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                    ->options(fn(Get $get) => $get('new_student') == true ? AcademicYear::where('is_registration_active',true)->where('is_upgrade_active',true)->pluck('name', 'id') : AcademicYear::where('is_upgrade_active',true)->pluck('name', 'id'))
                    ->default(AcademicYear::where('is_registration_active',true)->where('is_default',true)?->first()?->name)
                    ->required()
                    ->live(),
                    Select::make('academic_stage_id')->label(trans_choice('main.academic_stage',1))
                        ->options( AcademicStage::pluck('name', 'id'))
                        ->required()
                        ->live(),
                    Select::make('course_id')->label(trans_choice('main.academic_course',1))
                        ->options(fn (Get $get): Collection => Course::query()
                        ->where('academic_stage_id', $get('academic_stage_id'))
                        ->pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set,) {
                            $set('semester_id', null);
                        })
                        ,
                    Select::make('semester_id')->label(trans_choice('main.semester',1))->required()
                        ->options(fn (Get $get): Collection => Semester::query()
                        ->where('course_id', $get('course_id'))
                        ->where('is_registration_active', true)
                        ->pluck('name', 'id'))->live(),
                    ]),
                    DatePicker::make('enrollment_date')->label(trans('main.enrollment_date'))->required(),

            ])
            ->closeModalByClickingAway(false)
            ->action(function(array $arguments,array $data) {
                    //register it under a new semester
                    try{
                        DB::beginTransaction();
                        //get all the current_semester students
                        $semester = Semester::find($data['current_semester_id']);
                        $students = StudentSemester::where('semester_id', $semester->id)->pluck('student_id') ;
                        if($students->count() > 0){
                            $students = Student::where('id', $students)->whereDoesntHave('termination')->where('status','approved')->pluck('id') ;
                            
                        }
                        else{
                            $students = Student::where('semester_id', $semester->id)->whereDoesntHave('termination')
                            ->where('status','approved')->pluck('id') ;
                        }
                        foreach ($students as $student_id) {
                                StudentSemester::create([
                                    'student_id' => $student_id,
                                    'semester_id' => $data['semester_id'],
                                    'enrollment_date' => $data['enrollment_date'],
                                    'is_promoted' => true,
                                    'is_current' => false,
                            ]);
                        }

                        DB::commit();
                        Notification::make()
                        ->title(trans('main.student_upgraded_successfully'))
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
            // Actions\CreateAction::make(),
        ];
    }
}
