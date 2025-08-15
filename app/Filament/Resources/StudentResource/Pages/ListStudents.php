<?php

namespace App\Filament\Resources\StudentResource\Pages;

use Filament\Actions;
use App\Models\Course;
use Filament\Forms\Get;
use App\Models\Semester;
use App\Models\AcademicYear;
use Filament\Actions\Action;
use App\Models\AcademicStage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\StudentResource;
use Filament\Forms;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            // Action::make('upgrade_student')
            // ->color('success')
            // ->label(trans('main.upgrade_student'))
            // ->visible(employeeHasPermission('upgrade_student_student'))
            // ->form([
            //     Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
            //     ->options(fn(Get $get) => $get('new_student') == true ? AcademicYear::where('is_registration_active',true)->where('is_upgrade_active',true)->pluck('name', 'id') : AcademicYear::where('is_upgrade_active',true)->pluck('name', 'id'))
            //     ->default(AcademicYear::where('is_registration_active',true)->where('is_default',true)?->first()?->name)
            //     ->required()
            //     ->live(),
            //     Select::make('academic_stage_id')->label(trans_choice('main.academic_stage',1))
            //         ->options( AcademicStage::pluck('name', 'id'))
            //         ->required()
            //         ->live(),
            //     Select::make('course_id')->label(trans_choice('main.academic_course',1))
            //         ->options(fn (Get $get): Collection => Course::query()
            //         ->where('academic_stage_id', $get('academic_stage_id'))
            //         ->pluck('name', 'id'))
            //         ->required()
            //         ->live()
            //         ->afterStateUpdated(function (Forms\Set $set,) {
            //             $set('semester_id', null);
            //         })
            //         ,
            //     Select::make('semester_id')->label(trans_choice('main.semester',1))->required()
            //         ->options(fn (Get $get): Collection => Semester::query()
            //         ->where('course_id', $get('course_id'))
            //         ->where('is_registration_active', true)
            //         ->pluck('name', 'id'))->live(),
            //     DatePicker::make('enrollment_date')->label(trans('main.enrollment_date'))->required(),
            // ])
            // ->action(function(array $arguments,array $data) {
            //         //register it under a new semester
            //         try{
            //             DB::beginTransaction();
            //             $this->record->semesters()->create([
            //                 'semester_id' => $data['semester_id'],
            //                 'enrollment_date' => $data['enrollment_date'],
            //                 'is_promoted' => true,
            //                 'is_current' => false,
            //             ]);

            //             DB::commit();
            //             Notification::make()
            //             ->title(trans('main.student_upgraded_successfully'))
            //             ->icon('heroicon-o-document-text')
            //             ->iconColor('success')
            //             ->send();
            //         }
            //         catch(\Exception $ex)
            //         {
            //             DB::rollBack();
            //             Notification::make()
            //             ->title($ex)
            //             ->icon('heroicon-o-document-text')
            //             ->iconColor('danger')
            //             ->send();
            //         }
                
            

            // }),
            print_table_resrource_list('print_student',route('print_pdf',['type'=>"students"]))
        ];
    }
}
