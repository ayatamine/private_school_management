<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use App\Models\Department;
use App\Models\Designation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('designation_id')
            ->columns([
                Tables\Columns\TextColumn::make('department.name'),
                Tables\Columns\TextColumn::make('designation.name'),
                Tables\Columns\TextColumn::make('contract_start_date')->label(trans('main.contract_start_date'))->date(),
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
