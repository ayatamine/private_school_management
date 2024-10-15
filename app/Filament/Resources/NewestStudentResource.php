<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Course;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Semester;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ParentModel;
use App\Models\AcademicYear;
use App\Models\AcademicStage;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Request;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NewestStudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;

class NewestStudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

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
                Forms\Components\Select::make('registration_number')->label(trans('main.registration_number'))
                            ->preload()
                            ->options(Student::whereNull('semester_id')->pluck('registration_number', 'id'))
                            ->searchable()
                            ->columnSpanFull()
                            ->visible(fn (Get $get) => $get('new_student') == false )
                            ->hiddenOn('edit')
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                $student = Student::with('parent')->with('parent.user')->find($state);
                                // dd($parent);
                                $set('created_at', date('Y-m-d' ,strtotime($student?->created_at)) );
                                $set('academic_year_id', $student?->semester?->academic_year_id );
                                $set('opening_balance', $student?->opening_balance );
                                $set('finance_document', $student?->finance_document );
                                $set('note', $student?->note );
                                if($student?->finance_document !="" ) $set('finance_document', [ $student?->finance_document]);

                                $set('parent_id', $student?->parent?->id );
                                $set('parent_relation', $student?->parent?->relation ? trans("main.".$student?->parent?->relation) : "");
                                $set('parent_national_id', $student?->parent?->user->national_id);
                                $set('parent_email', $student?->parent?->user->email);
                                $set('parent_phone_number', $student?->parent?->user->phone_number);
                                $set('parent_gender', $student?->parent?->user?->gender ? trans("main.".$student?->parent?->user?->gender."") : "");
                                //set semester id
                                $set('semester_id', $student?->semester_id );
                            }),
                //when the student already registered
                Section::make(trans('main.radical_infos'))
                    ->columnSpanFull()
                    ->schema([ Grid::make()
                     ->schema([
                        Forms\Components\DatePicker::make('created_at')->label(trans('main.registration_date'))->columnSpanFull()->default(now()),
                        Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                            ->options(AcademicYear::where('is_registration_active',true)->pluck('name', 'id'))
                            ->default(AcademicYear::where('is_registration_active',true)->where('is_default',true)?->first()?->name)
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('semester_id')->label(trans_choice('main.semester',1))
                            ->options(fn (Get $get): Collection => Semester::query()
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->pluck('name', 'id')),
                            ])
                ])->visible(fn (Get $get) => $get('new_student') == false )
                ->hiddenOn('edit'),
                //new registration only form
                Section::make(trans('main.radical_infos'))
                    ->columnSpanFull()
                    ->schema([ Grid::make()
                     ->schema([
                        Forms\Components\DatePicker::make('created_at')->label(trans('main.registration_date'))->columnSpanFull()->default(now()),
                        Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                            ->options(AcademicYear::where('is_registration_active',true)->pluck('name', 'id'))
                            ->default(AcademicYear::where('is_registration_active',true)->where('is_default',true)?->first()?->name)
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('academic_stage_id')->label(trans_choice('main.academic_stage',1))
                            ->options( AcademicStage::pluck('name', 'id'))
                            ->live(),
                        Forms\Components\Select::make('course_id')->label(trans_choice('main.academic_course',1))
                            ->options(fn (Get $get): Collection => Course::query()
                            ->where('academic_stage_id', $get('academic_stage_id'))
                            ->pluck('name', 'id'))
                            ->live(),
                        Forms\Components\Select::make('semester')->label(trans_choice('main.semester',1))
                            ->options(fn (Get $get): Collection => Semester::query()
                            ->where('course_id', $get('course_id'))
                            ->pluck('name', 'id')),
                        Forms\Components\TextInput::make('first_name')->label(trans('main.first_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make( 'middle_name')->label(trans('main.middle_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('third_name')->label(trans('main.third_name'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')->label(trans('main.last_name'))
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')->label(label: trans('main.birth_date')),
                        Forms\Components\Select::make('nationality')->label(trans('main.nationality'))
                            ->options(
                                [
                                    'saudian'=>trans('main.saudian'),'other'=>trans('main.others')
                                ]
                            )
                            ->default('saudian')
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('nationality2')->label(trans('main.nationality'))
                            ->maxLength(255)
                            ->hidden(fn (Get $get) => $get('nationality') == 'saudian'),
                        Forms\Components\TextInput::make('national_id')->label(trans('main.national_id'))
                            ->required()
                            // ->unique(table:'users',ignoreRecord: true,column:'user.national_id')
                            ->maxLength(10),           
                        Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                            ->required()
                            // ->unique(table:'users',ignoreRecord: true)
                            ->maxLength(13),   
                        Forms\Components\TextInput::make('email')->label(trans('main.email'))
                            ->required(),
                            // ->unique(table:'users',ignoreRecord: true),   
                        Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                            ->options(['male'=>trans('main.male'), 'id'=>trans('main.female')])
                            ->required(),        
                        Forms\Components\TextInput::make('password')->label(trans('main.password'))->hint(trans('main.you_can_change_password'))
                            ->maxLength(255),        
                    ])
                ])
                ->visible(fn (Get $get) => $get('new_student') == null || $get('new_student') == true),
                Section::make(trans('main.parent_data'))
                    ->columnSpanFull()
                    ->schema([ Grid::make()
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
                                    ->unique(table:'users',ignoreRecord: true)
                                    ->maxLength(10),           
                                Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                                    ->required()
                                    ->unique(table:'users',ignoreRecord: true)
                                    ->maxLength(13),   
                                Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                                    ->options(['male'=>trans('main.male'), 'id'=>trans('main.female')])
                                    ->required(),        
                                Forms\Components\TextInput::make('email')->label(trans('main.email'))
                                    ->maxLength(255),   
                            ]),
                            //parent model only to show 
                            Forms\Components\Select::make('parent_relation')->label(trans('main.relation'))
                            ->options(
                                [
                                    'father'=>trans('main.father'),'mother'=>trans('main.mother'),'brother'=>trans('main.brother'),'sister'=>trans('main.sister'),'guardian'=>trans('main.guardian'),'other'=>trans('main.other')
                                ]
                            ),
                            Forms\Components\TextInput::make('parent_national_id')->label(trans('main.national_id'))->disabled(),
                            Forms\Components\TextInput::make('parent_email')->label(trans('main.email'))->disabled(),
                            Forms\Components\TextInput::make('parent_phone_number')->label(trans('main.phone_number'))->disabled(),
                            Forms\Components\TextInput::make('parent_gender')->label(trans('main.gender'))->disabled(),
                    
                        
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
                ->visible(fn (Get $get) => $get('new_student') == false )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Student::query()->whereNull('termination_reason'))
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label(trans('main.first_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('middle_name')->label(trans('main.middle_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('third_name')->label(trans('main.third_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')->label(trans('main.last_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nationality')->label(trans('main.nationality'))
                    ->formatStateUsing(fn (string $state) => $state == 'saudian' ? trans("main.$state") : $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.national_id')->label(trans('main.national_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.phone_number')->label(trans('main.phone_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.gender')->label(trans('main.gender'))
                    ->formatStateUsing(fn (string $state) => trans("main.$state"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('sttus')->label(trans('main.approvel_status'))
                    ->badge()
                    ->color(fn (string $state) => $state == 'rejected' ? "danger" : "success")
                    ->formatStateUsing(fn (string $state) => $state =='rejected' ? trans("main.rejected") : trans("main.approved"))
                    ->sortable(),
                Tables\Columns\TextColumn::make('registeredBy.username')->label(trans('main.registered_by'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.registration_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('semester_id')->label(trans_choice('main.semester',1))
                    ->relationship('semester', 'name')->searchable()
                    ->preload(),
                
                SelectFilter::make('gender')->label(trans('main.gender'))->options([
                    'male' => trans('main.male'),
                    'female' => trans('main.female'),
                ]),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
                                ->url(fn (Student $record): string => route('filament.admin.resources.students.edit', $record))
                        ])
                        ->columns(2)
                        ->id('main-section')
                        ->schema([
                                TextEntry::make('semester.academicYear.name')->label(trans_choice('main.academic_year',1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.name')->label(trans_choice('main.semester',number: 1))->weight(FontWeight::Bold),
                                TextEntry::make('first_name')->label(trans('main.first_name'))->weight(FontWeight::Bold),
                                TextEntry::make('middle_name')->label(trans('main.middle_name'))->weight(FontWeight::Bold),
                                TextEntry::make(name: 'third_name')->label(trans('main.third_name'))->weight(FontWeight::Bold),
                                TextEntry::make('last_name')->label(trans('main.last_name'))->weight(FontWeight::Bold),
                                TextEntry::make('nationality')->label(trans('main.nationality'))->weight(FontWeight::Bold),
                                TextEntry::make('user.national_id')->label(trans('main.national_id'))->weight(FontWeight::Bold),
                                TextEntry::make('user.gender')->label(trans('main.gender'))->weight(FontWeight::Bold),
                                TextEntry::make('user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                                TextEntry::make('user.email')->label(trans('main.email'))->weight(FontWeight::Bold),
                                TextEntry::make('created_at')->label(trans('main.registration_date'))->date()->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.parent_data'))
                        ->columns(2)
                        ->id('parent-section')
                        ->schema([
                                TextEntry::make('parent.full_name')->label(trans('main.full_name'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.national_id')->label(trans('main.national_id'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.email')->label(trans('main.email'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.gender')->label(trans('main.gender'))->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.financial_infos'))
                        ->columns(2)
                        ->id('financial-section')
                        ->schema([


                                TextEntry::make('opening_balance')->label(trans('main.opening_balance'))
                                ->formatStateUsing(fn (string $state) => $state." ".trans("main.".env('DEFAULT_CURRENCY')))
                                ->weight(FontWeight::Bold),
                                ViewEntry::make('finance_document')->label(trans('main.document'))->view('infolists.components.view-financial-document'),
                                TextEntry::make(name: 'note')->label(trans('main.note'))->weight(FontWeight::Bold)

                       
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.payments'))
                        ->id('payments-section')
                        ->schema([
                            ViewEntry::make('receiptVoucher')->label(trans_choice('main.payments',2))->view('infolists.components.student-payments')
                       
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.account_ballance'))
                        ->columns(2)
                        ->id('account_ballance-section')
                        ->schema([
                                TextEntry::make('balance')->label(trans('main.account_ballance_actual'))
                                ->color('primary')
                                ->size(TextEntry\TextEntrySize::Large)
                                ->weight(FontWeight::Bold)
                                ->tooltip(function (TextEntry $component): ?string {
                                    
                                    return trans('main.balance_calculate_method');
                                })
                       
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
