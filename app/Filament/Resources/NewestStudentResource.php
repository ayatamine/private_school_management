<?php

namespace App\Filament\Resources;

use Closure;
use Exception;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Semester;
use Filament\Forms\Form;
use App\Models\GeneralFee;
use App\Models\TuitionFee;
use Filament\Tables\Table;
use App\Models\ParentModel;
use App\Models\AcademicYear;
use App\Models\AcademicStage;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Request;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewestStudentResource\Pages;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use App\Filament\Resources\StudentResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class NewestStudentResource extends Resource implements HasShieldPermissions 
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'approve_registeration',
            'print'
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_any_newest::student');
    }
    public static function getNavigationGroup():string
    {
        return trans('main.student_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.student',1);
    }
    public static function getNavigationLabel():string
    {
        return trans('main.student_registration');
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.student',2);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('new_student')->label(trans('main.new_student'))
                ->live()
                ->default(true)
                ->hiddenOn('edit'),
                Forms\Components\Select::make('registration_number')->label(trans('main.id_number'))
                            ->preload()
                            ->searchable()
                            ->getSearchResultsUsing(function(string $search): array {
                                 return   Student::whereNull('semester_id')->where(function ($q) use ($search) {
                                                    $q->where('registration_number', 'like', "%{$search}%")
                                                    ->orWhere('username', 'like', "%{$search}%")
                                                    ->orWhere('last_name', 'like', "%{$search}%")
                                                    ->orWhere('third_name', 'like', "%{$search}%");
                                        })->pluck('registration_number', 'id')->toArray();
                            })
                            ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                            ->visible(fn (Get $get) => $get('new_student') == false )
                            ->hiddenOn('edit')
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $student = Student::with('user')->with('parent')->with('parent.user')->find($state);
                                // dd($parent);
                                $set('first_name', $student?->first_name);
                                $set('last_name', $student?->last_name);
                                $set('middle_name', $student?->middle_name);
                                $set('third_name', $student?->third_name);
                                $set('birth_date', date('Y-m-d' ,strtotime($student?->birth_date)));
                                $set('registration_number', $student?->registration_number);
                                $set('created_at', date('Y-m-d' ,strtotime($student?->created_at)) );
                                $set('academic_year_id', $student?->semester?->academic_year_id );
                                // $set('opening_balance', $student?->opening_balance );
                                // $set('note', $student?->note );
                                // if($student?->finance_document !="" ) $set('finance_document', [ $student?->finance_document]);

                                $set('national_id', $student?->user?->national_id );
                                $set('phone_number', $student?->user?->phone_number );
                                $set('email', $student?->user?->email );
                                $set('gender', $student?->user?->gender );

                                $set('parent_relation', $student?->parent?->relation ? trans("main.".$student?->parent?->relation) : "");
                                $set('parent_national_id', $student?->parent?->user->national_id);
                                $set('parent_email', $student?->parent?->user->email);
                                $set('parent_phone_number', $student?->parent?->user->phone_number);
                                $set('parent_gender', $student?->parent?->user?->gender ? trans("main.".$student?->parent?->user?->gender."") : "");
                               //set semester id
                               $set('semester_id', $student?->semester_id );
                            }),
                //when the student already registered
                // Section::make(trans('main.radical_infos'))
                //     ->columnSpanFull()
                //     ->schema([ Grid::make()
                //      ->schema([
                //         Forms\Components\DatePicker::make('created_at')->label(trans('main.registration_date'))->columnSpanFull()->default(now()),
                //         Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                //             ->options(AcademicYear::where('is_registration_active',true)->pluck('name', 'id'))
                //             ->default(AcademicYear::where('is_registration_active',true)->where('is_default',true)?->first()?->name)
                //             ->required()
                //             ->live(),
                //             Forms\Components\Select::make('semester_id')->label(trans_choice('main.semester',1))
                //             ->options(fn (Get $get): Collection => Semester::query()
                //             ->where('academic_year_id', $get('academic_year_id'))
                //             ->pluck('name', 'id')),
                //             ])
                // ])->visible(fn (Get $get) => $get('new_student') == false )
                // ->hiddenOn('edit'),
                //new registration only form
                Section::make(trans('main.radical_infos'))
                    ->columnSpanFull()
                    ->schema([ Grid::make()
                     ->schema([
                        Forms\Components\TextInput::make('id')->label(trans('main.id_number'))->default(Student::latest()->first()?->id + 1)->disabled(),
                        Forms\Components\DatePicker::make('created_at')->label(trans('main.registration_date'))->default(now()),
                        Grid::make()
                        ->columns(4)
                        ->schema([
                            Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                            ->options(AcademicYear::where('is_registration_active',true)->pluck('name', 'id'))
                            ->default(AcademicYear::where('is_registration_active',true)->where('is_default',true)?->first()?->name)
                            ->required()
                            ->live(),
                            Forms\Components\Select::make('academic_stage_id')->label(trans_choice('main.academic_stage',1))
                                ->options( AcademicStage::pluck('name', 'id'))
                                ->required()
                                ->live(),
                            Forms\Components\Select::make('course_id')->label(trans_choice('main.academic_course',1))
                                ->options(fn (Get $get): Collection => Course::query()
                                ->where('academic_stage_id', $get('academic_stage_id'))
                                ->pluck('name', 'id'))
                                ->required()
                                ->live(),
                            Forms\Components\Select::make('semester_id')->label(trans_choice('main.semester',1))
                                ->required()
                                ->options(fn (Get $get): Collection => Semester::query()
                                ->where('course_id', $get('course_id'))
                                ->where('is_registration_active', true)
                                ->pluck('name', 'id')),  
                        ]),
                        Grid::make()
                        ->columns(4)
                        ->schema([
                            Forms\Components\TextInput::make('first_name')->label(trans('main.first_name'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make( 'middle_name')->label(trans('main.middle_name'))
                                ->maxLength(255),
                            Forms\Components\TextInput::make('third_name')->label(trans('main.third_name'))
                                ->maxLength(255),
                            Forms\Components\TextInput::make('last_name')->label(trans('main.last_name'))
                                ->required()
                                ->maxLength(255),
                        ]),
                        Grid::make()
                        ->columns(4)
                        ->schema([
                            Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                            ->options(['male'=>trans('main.male'), 'female'=>trans('main.female')]),        
                            Forms\Components\DatePicker::make('birth_date')->label(label: trans('main.birth_date')),
                            Forms\Components\Select::make('nationality')->label(trans('main.nationality'))
                                ->options(
                                    [
                                        'saudian'=>trans('main.saudian'),'other'=>trans('main.others')
                                    ]
                                )
                                ->default('saudian')
                                ->hiddenOn('edit')
                                ->required()
                                ->live(),
                            Forms\Components\TextInput::make('nationality2')->label(trans('main.nationality'))
                                ->maxLength(255)
                                ->visible(fn (Get $get) => $get('nationality') != 'saudian')
                                ->hiddenOn('edit'),
                            Forms\Components\TextInput::make('nationality')->label(trans('main.nationality'))
                                ->maxLength(255)
                                ->required()
                                ->hiddenOn('create'),
                            Forms\Components\TextInput::make('national_id')->label(trans('main.national_id'))
                                ->required()
                                ->rules([
                                    fn (Student $student): Closure => function (string $attribute, $value, Closure $fail) use ($student) {
                                    
                                        if($student?->id)
                                        {
                                            if (User::whereNationalId($value)->whereNot('id',$student->user_id)->first() ) {
                                                $fail(trans('main.national_id_used_before'));
                                            }
                                        }else{
                                            if (User::whereNationalId($value)->first() ) {
                                                $fail(trans('main.national_id_used_before'));
                                            }
                                        }
                                    },
                                ])->maxLength(10),
                        ]),
                       
                        Grid::make()
                        ->columns(3)
                        ->schema([  
                            Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                                ->numeric()
                                // ->unique(table:'users',ignoreRecord: true)
                                ->maxLength(13),   
                            Forms\Components\TextInput::make('email')->label(trans('main.email')),
                                // ->unique(table:'users',ignoreRecord: true),   
                        
                            Forms\Components\TextInput::make('password')->label(trans('main.password'))
                                ->maxLength(255)
                                // ->hidden(fn (Get $get) => $get('new_student') == false)
                                ,        
                        ])
                                      
                        
                    ])
                ]),
                // ->visible(fn (Get $get) => $get('new_student') == null || $get('new_student') == true),
                Section::make(trans('main.parent_data'))
                    ->columnSpanFull()
                    ->schema([ 
                        Grid::make()
                        ->columns(3)
                        ->schema([
                            Forms\Components\Select::make('parent_id')->label(trans_choice('main.parent',1))
                                ->relationship('parent','full_name')
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set, $state) {
                                    $parent = ParentModel::with('user:id,national_id,email,gender,phone_number')->findOrFail($state);
                                    // dd($parent);
                                    $set('parent_relation', $parent->relation);
                                    $set('parent_national_id', $parent?->user->national_id);
                                    $set('parent_email', $parent?->user->email);
                                    $set('parent_phone_number', $parent?->user->phone_number);
                                    $set('parent_gender', trans("main.".$parent?->user?->gender.""));
                                })
                                ->searchable()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('full_name')->label(trans('main.full_name'))
                                    ->required()
                                    ->maxLength(255),
                                    Forms\Components\Select::make('relation')->label(trans('main.parent_relation'))
                                        ->options(
                                            [
                                                'father'=>trans('main.father'),'mother'=>trans('main.mother'),'brother'=>trans('main.brother'),'sister'=>trans('main.sister'),'guardian'=>trans('main.guardian'),'other'=>trans('main.other')
                                            ]
                                        )
                                        ->required(),
                                    Forms\Components\TextInput::make('national_id')->label(trans('main.national_id'))
                                        ->required()
                                        // ->unique(table:'users',ignoreRecord: true)
                                        ->maxLength(10),           
                                    Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                                        ->required()
                                        ->maxLength(13),   
                                    Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                                        ->options(['male'=>trans('main.male'), 'id'=>trans('main.female')])
                                        ->required(),        
                                    Forms\Components\TextInput::make('email')->label(trans('main.email'))
                                        ->maxLength(255),   
                                ]),
                                Forms\Components\TextInput::make('parent_national_id')->label(trans('main.national_id'))->disabled(),
                                Forms\Components\Select::make('parent_relation')->label(trans('main.relation'))
                                ->options(
                                    [
                                        'father'=>trans('main.father'),'mother'=>trans('main.mother'),'brother'=>trans('main.brother'),'sister'=>trans('main.sister'),'guardian'=>trans('main.guardian'),'other'=>trans('main.other')
                                    ]
                                ),
                               
                        
                            
                    ])
                        ]),
                Section::make(trans('main.financial_infos'))
                    ->columnSpanFull()
                    ->schema([ Grid::make()
                     ->schema([
                        Forms\Components\TextInput::make('opening_balance')->label(trans('main.opening_balance'))
                            ->numeric(),
                        Forms\Components\FileUpload::make(name: 'finance_document')->label(trans('main.document'))
                            ->directory('students_financial_documents'),
                        Forms\Components\Textarea::make('note')->label(trans('main.note'))
                            ->columnSpanFull()
                            ->maxLength(255),
                        
                ])
                ])
                // ->visible(fn (Get $get) => $get('new_student') == false )
                ->hidden()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Student::query()->whereNull('termination_reason'))
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')->label(trans('main.id_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student_name')->label(trans('main.student_name'))
                    ->state(fn (Student $student) => $student?->first_name .' '.$student?->last_name)
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.national_id')->label(trans('main.national_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nationality')->label(trans('main.nationality'))
                    ->formatStateUsing(fn (string $state) => $state == 'saudian' ? trans("main.$state") : $state)
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('user.course_enrolled')->label(trans('main.course_enrolled'))
                    ->state(fn (Student $student) => $student?->semester?->academicYear?->name .' '.$student?->semester?->course?->name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->label(trans('main.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                            'pending'=>'primary',
                            'approved'=>'success',
                            'rejected'=>'danger',

                    })
                    ->formatStateUsing(fn (string $state) =>trans("main.$state"))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.registration_date'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')->label(trans('main.approvel_date'))
                    ->date('Y-m-d')
                    ->sortable(),
               
            ])
            ->filters([
                SelectFilter::make('course_id')->label(trans('main.course_enrolled'))
                    ->options(Course::pluck('name','id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                
                        if ($data['value'] == null) {
                            return $query;
                        }
                
                        return $query->whereHas('semester', function ($query) use ($data) {
                            return $query->whereNull('termination_reason')->where('course_id', $data['value']);
                        });
                    }),
                SelectFilter::make('status')->label(trans('main.status'))
                ->options(['pending'=>trans('main.pending'),'approved'=>trans('main.approved'), 'rejected'=>trans('main.rejected')]),
            ])
            ->deferFilters()
            ->filtersApplyAction(
                fn (\Filament\Tables\Actions\Action $action) => $action
                    ->label(trans('main.apply')),
            )
            ->actions([
                Tables\Actions\Action::make('registeration_action')
                // ->visible(fn(Student $record)=>$record->is_banned == null)
                ->label(trans('main.registeration_action'))
                ->visible(employeeHasPermission('approve_registeration_newest::student'))
                ->icon('heroicon-o-check')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make(name: 'status')->label(trans('main.approvel_status'))
                    ->options(['approved'=>trans('main.approve'), 'rejected'=>trans('main.reject')])
                    ->live()
                    ->required(),  
                    Forms\Components\DatePicker::make(name: 'approved_at')->label(trans('main.approvel_date'))
                    ->required()
                    ->hidden(fn(Get $get)=>$get('status') == "rejected"),  
                ])
                ->action(function(Student $Student,array $data){
                    
                    try{
                        DB::beginTransaction();
                        $Student->update($data);
                        if($data['status'] == "approved")
                        {
                            // add tuiton fees
                            $tuitionFee = TuitionFee::whereCourseId($Student?->semester?->course_id)->first();
                            if(!$Student?->semester)
                            {
                                Notification::make()
                                    ->title(trans('main.student_not_yet_attached_to_course'))
                                    ->icon('heroicon-o-document-text')
                                    ->iconColor('danger')
                                    ->send();
                                return redirect()->route('filament.admin.resources.newest-students.edit',['record'=>$Student?->id]);
                            }
                            if($tuitionFee)
                            {
                                $Student->tuitionFees()->sync($tuitionFee->id);
                            }
                            // add other fees
                            // add other fees
                            $generalFees = GeneralFee::whereCourseId($Student?->semester?->course_id)->get();
                            if($generalFees)
                            {
                                foreach($generalFees as $fee)
                                {
                                    $Student->otherFees()->sync($fee->id);
                                    $discounts = $fee->payment_partition;
                                    $discounts[0]['discount_type'] = "percentage";
                                    $discounts[0]['discount_value'] = 0;
                                    DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',[json_encode($discounts),$fee->id,GeneralFee::class,$Student->id]);

                                }
                                
                            }
                            // add concession fees
                        
                            $discounts = $tuitionFee->payment_partition;
                           
                            $discounts[0]['discount_type'] = "percentage";
                            $discounts[0]['discount_value'] = 0;
                            
                            if($tuitionFee) DB::update('update student_fee set discounts = ? where feeable_id = ? AND feeable_type = ? AND student_id = ?',[json_encode($discounts),$tuitionFee->id,TuitionFee::class,$Student->id]);
                            
                          
                            //create invoice for student
                            $academic_year_id = $Student->semester?->academicYear?->id;
                            $invoice  = Invoice::whereStudentId($Student->id)->whereAcademicYearId($academic_year_id)->first();
                            if(!$invoice)
                            {
                                $invoice =Invoice::create([
                                    'number'=>$Student->semester?->academicYear?->name."".$Student->registration_number,
                                    'name' => trans('main.fees_invoice')." ".$Student->semester?->academicYear?->name,
                                    'student_id'=>$Student->id,
                                    'academic_year_id'=>$academic_year_id,
                                ]);
                                $Student->invoices()->save($invoice);
                            }
                        }
                            Notification::make()
                                ->title(trans('main.student_status_changed_successfully'))
                                ->icon('heroicon-o-document-text')
                                ->iconColor('success')
                                ->send();
                            DB::commit();
                        
                    }
                    catch(Exception $ex)
                    {
                        DB::rollBack();
                        dd($ex);
                        Notification::make()
                            ->title($ex)
                            ->icon('heroicon-o-document-text')
                            ->iconColor('danger')
                            ->send();
                    }
                })
                ->hidden(fn (Student $student) =>$student->status == "approved"),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(fn()=>employeeHasPermission('print_newest::student'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.income',2)
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
                            Action::make(trans('main.edit'))
                                ->url(fn (Student $record): string => route('filament.admin.resources.newest-students.edit', $record)),
                            Action::make(trans('main.delete'))
                                ->color('danger')
                                ->requiresConfirmation()
                                ->action(function(Student $student){
                                     $student->delete();
                                     return redirect()->route('filament.admin.resources.newest-students.index');
                                })
                        ])
                        ->columns(3)
                        ->id('main-section')
                        ->schema([
                            TextEntry::make('registration_number')->label(trans('main.registration_number'))
                            ->formatStateUsing(fn($state)=>$state."#")
                            ->weight(FontWeight::Bold)
                            ->size(TextEntrySize::Large)
                            ->color('danger'),
                            TextEntry::make('created_at')->label(trans('main.registration_date'))->date('Y-m-d')->weight(FontWeight::Bold),
                            TextEntry::make('approved_at')->label(trans('main.approvel_date'))->date('Y-m-d')->weight(FontWeight::Bold),
                            TextEntry::make('name')->label(trans('main.name'))
                            ->columnSpanFull()
                            ->weight(FontWeight::Bold)
                            ->state(fn (Student $student) => $student?->first_name .' '.$student?->middle_name .' '.$student?->third_name .' '.$student?->last_name)
                            ->columnSpanFull(),
                            TextEntry::make('user.gender')->label(trans('main.gender'))
                            ->formatStateUsing(fn($state)=> trans('main.'.$state))
                            ->weight(FontWeight::Bold),
                            TextEntry::make('nationality')->label(trans('main.nationality'))
                                ->formatStateUsing(fn($state)=>$state == "saudian" ? trans('main.saudian') : $state)
                                ->weight(FontWeight::Bold),
                            TextEntry::make('user.national_id')->label(trans('main.national_id_n'))->weight(FontWeight::Bold),
                            TextEntry::make('user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                            TextEntry::make('user.email')->label(trans('main.email'))->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.parent_data'))
                        ->columns(3)
                        ->id('parent-section')
                        ->schema([
                                TextEntry::make('parent.full_name')->label(trans('main.full_name'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.national_id')->label(trans('main.national_id_n'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.relation')->label(trans('main.relation'))
                                ->formatStateUsing(fn($state)=>  trans('main.'.$state) )
                                ->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.email')->label(trans('main.email'))->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.academic_data'))
                        ->columns(6)
                        ->id('parent-secdtion')
                        ->schema([
                            TextEntry::make('registration_number')->label(trans('main.id_ne'))->weight(FontWeight::Bold),
                            TextEntry::make('semester.academicYear.name')->label(trans_choice('main.academic_year',1))->weight(FontWeight::Bold),
                            TextEntry::make('semester.course.academicStage.name')->label(trans_choice('main.academic_stage',1))->weight(FontWeight::Bold),
                            TextEntry::make('semester.course.name')->label(trans_choice('main.academic_course',number: 1))->weight(FontWeight::Bold),
                            TextEntry::make('semester.name')->label(trans_choice('main.semester',number: 1))->weight(FontWeight::Bold),
                            TextEntry::make('approved_at')->label(trans('main.approved_at'))->date('Y-m-d')->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.financial_infos'))
                        ->columns(3)
                        ->id('financial-section')
                        ->schema([


                            TextEntry::make('opening_balance')->label(trans('main.opening_balance'))
                            ->formatStateUsing(fn (string $state) => $state." ".trans("main.".env('DEFAULT_CURRENCY')))
                            ->weight(FontWeight::Bold),
                            TextEntry::make(name: 'note')->label(trans('main.note'))->weight(FontWeight::Bold),
                            ViewEntry::make('finance_document')->label(trans('main.document'))->view('infolists.components.view-financial-document'),
                            TextEntry::make('balance')->label(trans('main.account_ballance_actual'))
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->formatStateUsing(fn(string $state)=>$state." "." " .trans("main.".env('DEFAULT_CURRENCY').""))
                            ->tooltip(function (TextEntry $component): ?string {
                                
                                return trans('main.balance_calculate_method');
                            }),
                            TextEntry::make('total_fees_after_due_date')->label(trans('main.total_fees_to_pay'))
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->formatStateUsing(fn(string $state)=>$state." "." " .trans("main.".env('DEFAULT_CURRENCY').""))
                            ->tooltip(function (TextEntry $component): ?string {
                                
                                return trans('main.total_fees_to_pay_method');
                            }),
                            TextEntry::make('total_fees_rest')->label(trans('main.total_fees_rest'))
                            ->color('primary')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight(FontWeight::Bold)
                            ->formatStateUsing(fn(string $state)=>$state." "." " .trans("main.".env('DEFAULT_CURRENCY').""))
                            ->tooltip(function (TextEntry $component): ?string {
                                
                                return trans('main.total_fees_rest_method');
                            })

                       
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.payments'))
                        ->id('payments-section')
                        ->schema([
                            ViewEntry::make('receiptVoucher')->label(trans_choice('main.payments',2))->view('infolists.components.student-payments')
                       
                        ]),
                \Filament\Infolists\Components\Section::make(trans_choice('main.tuition_fee',2))
                        ->id('tuition_fee-section')
                        ->schema([

                                ViewEntry::make('tuitionFees')->label(trans_choice('main.tuition_fee',2))->view('infolists.components.view-student-tuition-fees')
                                // ->registerActions([
                                //     Action::make('editPartitions')
                                //         ->label(trans('main.edit_partitions'))
                                //         ->form([
                                //             Forms\Components\Select::make('name')
                                //                 ->required(),
                                //         ])
                                //         ->action(function (array $data, Student $record) {
                                //             $record->status()->create($data);
                                //         }),
                                // ]),
                        ]),
                \Filament\Infolists\Components\Section::make(trans_choice('main.transport_fee',2))
                        ->id('transport_fee-section')
                        ->schema([

                                ViewEntry::make('transportFees')->label(trans_choice('main.transport_fee',2))->view('infolists.components.view-student-transport-fee')
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
            'index' => Pages\ListNewestStudents::route('/'),
            'create' => Pages\CreateNewestStudent::route('/create'),
            'edit' => Pages\EditNewestStudent::route('/{record}/edit'),
            'view' => Pages\ViewNewestStudent::route('/{record}'),
        ];
    }
}
