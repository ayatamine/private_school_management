<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\EmployeeResource\RelationManagers\EmploymentDurationRelationManager;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'icon-employees';
    public static function getNavigationGroup():string
    {
        return trans('main.human_resource');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.employee',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.employee',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.employee',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('new_employee')->label(trans('main.new_employee'))
                        ->schema([
                            Forms\Components\Toggle::make('new_employee')->label(trans('main.new_employee'))
                            ->live()
                            ->default(true)
                            ->hiddenOn('view')
                            ->hiddenOn('edit')
                            ->afterStateUpdated(function (Set $set, $state) {
                                if($state == true) return redirect()->route('filament.admin.resources.employees.create');
                            }),
                            Forms\Components\Select::make('registration_number')->label(trans('main.registration_number'))
                                        ->preload()
                                        ->options(Employee::whereDoesntHave('employmentDuration')->pluck('full_name', 'id'))
                                        ->searchable()
                                        ->columnSpanFull()
                                        ->visible(fn (Get $get) => $get('new_employee') == false )
                                        ->hiddenOn('edit')
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, $state) {
                                            $employee = Employee::with('user')->find($state);
                                            // dd($parent);
                                            // $set('created_at', date('Y-m-d' ,strtotime($employee?->created_at)) );
                                            $set('id', $employee?->id );
                                            $set('first_name', $employee?->first_name );
                                            $set('middle_name', $employee?->middle_name );
                                            $set('third_name', $employee?->third_name );
                                            $set('last_name', $employee?->last_name );
                                            $set('last_name', $employee?->last_name );
                                            $set('gender', $employee?->gender );
                                            $set('last_name', $employee?->last_name );
                                            $set('email', $employee?->user?->email );
                                            $set('phone_number', $employee?->user?->phone_number );
                                            $set('national_id', $employee?->user?->national_id );
                                            $set('identity_type', $employee?->identity_type );
                                            $set('identity_expire_date', date('Y-m-d',strtotime($employee?->identity_expire_date )));
                                            // $set('password', $employee?->user?->password );


                                            if($employee?->documents !="" ) $set('documents', [ $employee?->documents]);

                                            $set('birth_date', date('Y-m-d',strtotime($employee?->birth_date)));
                                            $set('age',  (new Carbon($employee?->birth_date))->diffInYears(Carbon::now())." ".trans_choice('main.year',2) );
                                            $set('social_status', $employee?->social_status );
                                            $set('study_degree', $employee?->study_degree);
                                            $set('study_speciality', $employee?->study_speciality);
                                            $set('national_address', $employee?->national_address);
                                            $set('iban', $employee?->iban);
                                        }),
                            Section::make()
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('id')->label(trans('main.registration_number'))->default(Employee::latest()->first()?->id + 1)->disabled(),
                                Forms\Components\TextInput::make('code')->label(trans('main.prefix_code'))->default('EM')->hidden(true),
                                Forms\Components\TextInput::make('first_name')->label(trans('main.first_name'))->required(),
                                Forms\Components\TextInput::make('middle_name')->label(trans('main.middle_name'))->required(),
                                Forms\Components\TextInput::make('third_name')->label(trans('main.third_name')),
                                Forms\Components\TextInput::make('last_name')->label(trans('main.last_name')),
                                Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                                        ->options(['male'=>trans('main.male'), 'id'=>trans('main.female')])
                                        ->required(), 
                                Forms\Components\TextInput::make('email')->label(trans('main.email'))
                                ->maxLength(255),        
                                Forms\Components\TextInput::make('password')->label(trans('main.password'))->hint(trans('main.you_can_change_password'))->hiddenOn('edit')
                                    ->maxLength(255), 
                                Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                                            ->required()
                                            // ->unique(table:'users',ignoreRecord: true)
                                            ->maxLength(13),  
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
                                Forms\Components\Select::make(name: 'identity_type')->label(trans('main.identity_type'))
                                    ->options(['national_identity'=>trans('main.national_identity'), 'resident_accommodation'=>trans('main.resident_accommodation'), 'visitor_accommodation'=>trans('main.visitor_accommodation')])
                                    ->required(),  
                                    Forms\Components\TextInput::make('national_id')->label(trans('main.national_id'))
                                    ->required()
                                    // ->unique(table:'users',ignoreRecord: true)
                                    ->maxLength(10), 
                                Forms\Components\DatePicker::make('identity_expire_date')->label(trans('main.identity_expire_date'))->required(),

                                
                                // Forms\Components\DatePicker::make('joining_date')->label(trans('main.joining_date')),
                                Forms\Components\DatePicker::make('birth_date')->label(trans('main.birth_date'))
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $set('age',(new Carbon($state))->diffInYears(Carbon::now())." ".trans_choice('main.year',2));
                                            }),
                                Forms\Components\TextInput::make('age')->label(trans('main.age')),
                                Forms\Components\Select::make('social_status')->label(trans('main.social_status'))
                                    ->options([
                                        'single' =>trans('main.single'),
                                        'marrieed' =>trans('main.married')
                                    ]),
                                Forms\Components\TextInput::make('study_degree')->label(trans('main.study_degree')),
                                Forms\Components\TextInput::make('study_speciality')->label(trans('main.study_speciality')),
                                Forms\Components\TextInput::make('national_address')->label(trans('main.national_address')),
                                Forms\Components\TextInput::make('iban')->label(trans('main.iban')),
                                // Forms\Components\FileUpload::make('documents')
                                //     ->label(trans('main.documents'))
                                //     ->directory('employees')
                                //     ->columnSpanFull()
                                //     ->multiple(),
                                
                    
                            ])
                                   
            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(trans('main.registration_number'))
                    ->searchable()
                    ->sortable(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.national_id')->label(trans('main.national_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.phone_number')->label(trans('main.phone_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.gender')->label(trans('main.gender'))
                    ->formatStateUsing(fn (string $state) => trans("main.$state"))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.registration_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                // SelectFilter::make('account_status')->label('Account Status')->options([
                //     'pending' => 'Pending',
                //     'accepted' => 'Accepted',
                //     'blocked' => 'Blocked',
                // ]),
                SelectFilter::make('gender')->label(trans('main.gender'))->options([
                    'male' => trans('main.male'),
                    'female' => trans('main.female'),
                ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()

            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.designation',2)
                ])->disableXlsx(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmploymentDurationRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
            'view' => Pages\ViewEmployee::route('/{record}'),
        ];
    }
}
