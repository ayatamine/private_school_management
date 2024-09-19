<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'icon-employees';
    public static function getNavigationGroup():string
    {
        return trans('main.employee_settings');
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
                Forms\Components\TextInput::make('code')->label(trans('main.prefix_code'))->default('EM'),
                Forms\Components\TextInput::make('first_name')->label(trans('main.first_name'))->required(),
                Forms\Components\TextInput::make('middle_name')->label(trans('main.middle_name'))->required(),
                Forms\Components\TextInput::make('third_name')->label(trans('main.third_name')),
                Forms\Components\TextInput::make('last_name')->label(trans('main.last_name')),
                Forms\Components\Select::make(name: 'gender')->label(trans('main.gender'))
                        ->options(['male'=>trans('main.male'), 'id'=>trans('main.female')])
                        ->required(), 
                Forms\Components\TextInput::make('email')->label(trans('main.email'))
                ->maxLength(255),        
                Forms\Components\TextInput::make('password')->label(trans('main.password'))->hint(trans('main.you_can_change_password'))
                    ->maxLength(255), 
                Forms\Components\TextInput::make('phone_number')->label(trans('main.phone_number'))
                            ->required()
                            ->unique(table:'users',ignoreRecord: true)
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
                    ->unique(table:'users',ignoreRecord: true)
                    ->maxLength(10), 
                Forms\Components\DatePicker::make('identity_expire_date')->label(trans('main.identity_expire_date')),

                Forms\Components\Select::make('department_id')->label(trans_choice('main.department',1))
                ->relationship('department', 'name')
                ->required(),
                Forms\Components\Select::make('designation_id')->label(trans_choice('main.designation',1))
                ->relationship('designation', 'name')
                ->required(),
                Forms\Components\DatePicker::make('joining_date')->label(trans('main.joining_date')),
       
 
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
