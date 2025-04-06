<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Course;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AcademicYear;
use App\Models\AcademicStage;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\TuitionFeeReports;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TuitionFeeReportsResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\TuitionFeeReportsResource\RelationManagers;

class TuitionFeeReportsResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'icon-reports';

    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
   
   
    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false;
    // }
    public static function getModelLabel():string
    {

        
        return trans_choice('main.tuition_fee_reports',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.tuition_fee_reports',2);
    }

    public static function getPluralModelLabel():string
    {    $url =url()->previous();
        $parsedUrl = parse_url($url);
        if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
        $academic_year_id = $queryParams['tableFilters']['academic_year_id']['value'] ?? null;
    
        $academic_year = "";
        if($academic_year_id){
            $academic_year = AcademicYear::find($academic_year_id)?->name;
            $academic_year = "- ".trans_choice('main.academic_year',1) ."". $academic_year;
        }
        return trans_choice('main.tuition_fee_reports',2) ."" . $academic_year;
    }

    public static function canCreate():bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super-admin') || (employeeHasPermission('view_any_tuition::fee::reports'));
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'print',
        ];
    }
    public static function table(Table $table): Table
    {
        return $table
            ->query(function(Builder $query) {
                return Student::whereDoesntHave('termination')->where('status','approved');
            })
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')->label(trans('main.id_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')->label(trans('main.student_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.course_enrolled')->label(trans('main.course_enrolled'))
                    ->state(fn (Student $student) => $student?->semester?->academicYear?->name .' '.$student?->semester?->course?->name), 
                Tables\Columns\TextColumn::make('total_fees')->label(trans('main.total_fees'))
                    ->getStateUsing(function(Student $record) {
                        return number_format(calculateAllFees($record)['totals']['grand_total'],2,',',',')." ".trans('main.'.env('DEFAULT_CURRENCY'));
                }), 
                Tables\Columns\TextColumn::make('total_paid_fees')->label(trans('main.totalPayment'))
                    ->getStateUsing(function(Student $record) {
                        return number_format($record->payments()    ,2,',',',')." ".trans('main.'.env('DEFAULT_CURRENCY'));
                }),
                
                Tables\Columns\TextColumn::make('current_balance')->label(trans('main.current_balance'))
                    ->getStateUsing(function(Student $record) {
                        return number_format($record->opening_balance + calculateAllFees($record)['totals']['grand_total'] -$record->payments()     ,2,',',',') ." ".trans('main.'.env('DEFAULT_CURRENCY'));
                }),
                Tables\Columns\TextColumn::make('need_to_pay_balance')->label(trans('main.need_to_pay_balance'))
                    ->getStateUsing(function(Student $record) {
                        return number_format($record->opening_balance + calculateAllFees($record)['totals']['total_fees_to_pay'] -$record->payments()     ,2,',',',') ." ".trans('main.'.env('DEFAULT_CURRENCY'));
                }),
            //     Tables\Columns\TextColumn::make('value')->label(trans('main.value'))
            //     ->summarize(
            //         Sum::make()->query(fn ($query) => $query)->numeric(
            //                     2,',',',')
            //    )->suffix(' '.trans('main.'.env('DEFAULT_CURRENCY')))
            ])
            ->filters([
                SelectFilter::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                    ->relationship('semester.academicYear', 'name')->searchable()
                    ->preload(),
                SelectFilter::make('academic_stage_id')->label(trans_choice('main.academic_stage',1))
                    ->options(AcademicStage::pluck('name','id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                
                        if ($data['value'] == null) {
                            return $query;
                        }
                        //semester->course->academic_stage
                        //select courses where academic_stage_id = $data['value']
                       $courses = Course::whereHas('academicStage', function ($query) use ($data) {
                            return $query->where('academic_stage_id', $data['value']);
                        })->pluck('id');
                        return $query->whereHas('semester', function ($query) use ($data,$courses) {
                            return $query->whereIn('course_id', $courses);
                        });
                    }),
                SelectFilter::make('semester_id')->label(trans_choice('main.semester',1))
                    ->relationship('semester', 'name')->searchable()
                    ->preload(),
                SelectFilter::make('course_id')->label(trans('main.course_enrolled'))
                    ->options(Course::pluck('name','id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                
                        if ($data['value'] == null) {
                            return $query;
                        }
                
                        return $query->whereHas('semester', function ($q) use ($data) {
                            return $q->where('course_id', $data['value']);
                        });
                    }),
                //add nationality saudian or other
                SelectFilter::make('nationality')->label(trans('main.nationality'))
                ->options([
                    'saudian'=>trans('main.saudian'),
                    'other'=>trans('main.other')
                ])
                ->query(function (Builder $query, array $data): Builder {
            
                    if ($data['value'] == null) {
                        return $query;
                    }
            
                    return $data['value'] == 'saudian' ? $query->where('nationality', 'saudian') : $query->where('nationality', '!=', 'saudian');
                }),
                // Filter::make('created_at')
                // ->form([
                //     TextInput::make('from')->numeric()->label(trans('main.fees_from')),
                //     TextInput::make('to')->numeric()->label(trans('main.fees_to')),
                // ])
                // ->query(function (Builder $query, array $data): Builder {
                //     return $query
                //         ->when(
                //             $data['to'],
                //             fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                //         )
                //         ->when(
                //             $data['created_until'],
                //             fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                //         );
                // })
            ])
            ->deferFilters()
            ->filtersApplyAction(
                fn (\Filament\Tables\Actions\Action $action) => $action
                    ->label(trans('main.apply')),
            )
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn(Student $record) => route('filament.admin.resources.students.view', $record->id)),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(employeeHasPermission('print_tuition::fee::reports'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.tuition_fee_reports',2)
                ])->disableXlsx(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make(trans('main.radical_infos'))
                        ->headerActions([
                          
                        ])
                        ->columns(2)
                        ->id('main-section')
                        ->schema([
                                TextEntry::make('first_name')->label(trans('main.first_name'))->weight(FontWeight::Bold),
                                TextEntry::make('middle_name')->label(trans('main.middle_name'))->weight(FontWeight::Bold),
                                TextEntry::make(name: 'third_name')->label(trans('main.third_name'))->weight(FontWeight::Bold),
                                TextEntry::make('last_name')->label(trans('main.last_name'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.full_name')->label(trans('main.parent'))->weight(FontWeight::Bold),
                                TextEntry::make('semester.academicYear.name')->label(trans_choice('main.academic_year',1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.course.academicStage.name')->label(trans_choice('main.academic_stage',1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.course.name')->label(trans_choice('main.academic_course',number: 1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.name')->label(trans_choice('main.semester',number: 1))->weight(FontWeight::Bold),
                                TextEntry::make('nationality')->label(trans('main.nationality'))->weight(FontWeight::Bold),
                                TextEntry::make('user.national_id')->label(trans('main.national_id'))->weight(FontWeight::Bold),
                                TextEntry::make('user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans_choice('main.tuition_fee',2))
                        ->id('tuition_fee-section')
                        ->schema([

                                ViewEntry::make('tuitionFees')->label(trans_choice('main.tuition_fee',2))->view('infolists.components.view-student-tuition-fees-reports')

                        ]),
                       
            ]);
    }
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTuitionFeeReports::route('/'),
            'create' => Pages\CreateTuitionFeeReports::route('/create'),
            'edit' => Pages\EditTuitionFeeReports::route('/{record}/edit'),
            'view' => Pages\ViewTuitionFeeReport::route('/{record}'),
        ];
    }
}
