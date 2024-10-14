<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use App\Models\Designation;
use App\Models\EmploymentDuration;
use BladeUI\Icons\Components\Icon;
use Filament\Notifications\Notification;
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
                Forms\Components\DatePicker::make('contract_end_date')->label(trans('main.contract_end_date'))
                    ->visible(fn(EmploymentDuration $record)=>$record->contract_end_date != null)
                    ->required(),
                Forms\Components\TextInput::make('contract_end_reason')->label(trans('main.contract_end_reason'))
                    ->visible(fn(EmploymentDuration $record)=>$record->contract_end_date != null)
                    ->required(),
                Forms\Components\TextInput::make('note')->label(trans('main.note'))
                    ->visible(fn(EmploymentDuration $record)=>$record->contract_end_date != null),
                Forms\Components\FileUpload::make('attachment')
                    ->visible(fn(EmploymentDuration $record)=>$record->contract_end_date != null)
                    ->label(trans('main.attachment'))
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()->visible(fn(EmploymentDuration $record)=>$record->contract_end_date == null),
                Tables\Actions\Action::make('end_duration')
                ->visible(fn(EmploymentDuration $record)=>$record->contract_end_date == null)
                ->label(trans('main.end_employment_duration'))
                ->icon('icon-close')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Forms\Components\DatePicker::make('contract_end_date')->label(trans('main.contract_end_date'))->required(),
                    Forms\Components\TextInput::make('contract_end_reason')->label(trans('main.contract_end_reason'))->required(),
                    Forms\Components\TextInput::make('note')->label(trans('main.note')),
                    Forms\Components\FileUpload::make('attachment')
                    ->label(trans('main.attachment'))
                    ->columnSpanFull(),
                ])
                ->action(function(EmploymentDuration $record,array $data){
                    $record->update([
                        'contract_end_date'=>$data['contract_end_date'],
                        'contract_end_reason'=>$data['contract_end_reason'],
                        'note'=>$data['note'],
                        'attachment'=>$data['attachment']
                    ]);

                    Notification::make()
                        ->title(trans('main.end_employment_duration_successfully'))
                        ->icon('heroicon-o-document-text')
                        ->iconColor('success')
                        ->send();
                }),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
