<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\EmploymentDuration;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use App\Models\Designation;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EmploymentDurationRelationManager extends RelationManager
{
    protected static string $relationship = 'employmentDuration';
    protected static function getLabel(): ?string
    {
        return  trans_choice('main.employment_duration',1);
    }
    protected static function getpluralModelLabel(): ?string
    {
        return trans_choice('main.employment_duration',2);
    }
    protected static function getModelLabel(): ?string
    {
        return trans_choice('main.employment_duration',1);
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->hidden(fn(EmploymentDuration $employmentDuration) =>$employmentDuration->contract_end_date != null)
                ->schema([
                Forms\Components\Select::make('department_id')->label(trans_choice('main.department',1))
                ->options(Department::pluck('name','id'))
                ->required(),
                Forms\Components\Select::make('designation_id')->label(trans_choice('main.designation',1))
                    ->options(Designation::pluck('name','id'))
                    ->required(),
                Forms\Components\DatePicker::make('contract_start_date')->label(trans('main.contract_start_date')),
                Forms\Components\FileUpload::make('contract_image')
                    ->label(trans('main.employment_contract_image'))
                    ->image()
                    ->columnSpanFull(),
                ]),
                Section::make()
                ->hidden(fn(EmploymentDuration $employmentDuration) =>$employmentDuration->contract_end_date == null)
                ->schema([
                    Forms\Components\DatePicker::make('contract_end_date')->label(trans('main.contract_end_date'))->visibleOn('view'),
                    Forms\Components\TextInput::make('contract_end_reason')->label(trans('main.contract_end_reason'))->visibleOn('view'),
                    Forms\Components\TextInput::make('note')->label(trans('main.note'))->visibleOn('view'),
                    Forms\Components\TextInput::make('attachment')->label(trans('main.contract_end_attachment'))->visibleOn('view'),
                ])

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('designation_id')
            ->columns([
                Tables\Columns\TextColumn::make('department.name')->label(trans_choice('main.designation',1)),
                Tables\Columns\TextColumn::make('designation.name')->label(trans_choice('main.contract_start_date',1)),
                Tables\Columns\TextColumn::make('contract_start_date')->label(trans('main.contract_start_date'))->date(),
                Tables\Columns\TextColumn::make('contract_end_date')->label(trans('main.contract_end_date'))
                        ->formatStateUsing(fn (string $state) => $state ?? trans("main.employment_duration_active"))
                        ->date(),
                Tables\Columns\TextColumn::make('contract_end_date')->label(trans('main.contract_end_date'))
                        ->formatStateUsing(fn (string $state) => $state ?? trans("main.employment_duration_active"))
                        ->date(),
                // Tables\Columns\TextColumn::make('start_date'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
