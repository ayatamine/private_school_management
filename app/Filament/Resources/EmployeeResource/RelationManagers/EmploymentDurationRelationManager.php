<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use App\Models\Designation;
use App\Models\EmploymentDuration;
use BladeUI\Icons\Components\Icon;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class EmploymentDurationRelationManager extends RelationManager
{
    protected static string $relationship = 'employmentDurations';
    protected static bool $canCreateAnother = false;
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
    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
      return __('main.employment_history');
    }
    public function isReadOnly(): bool { return false; }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                // ->hidden(fn(EmploymentDuration $employmentDuration) =>$employmentDuration->contract_end_date != null)
                ->schema([
                Forms\Components\Select::make('department_id')->label(trans_choice('main.department',1))
                ->options(Department::pluck('name','id'))
                ->required(),
                Forms\Components\Select::make('designation_id')->label(trans_choice('main.designation',1))
                    ->options(Designation::pluck('name','id'))
                    ->required(),
                Forms\Components\DatePicker::make('contract_start_date')->label(trans('main.contract_start_date')),
                Forms\Components\FileUpload::make('contract_image')
                    ->directory('employees')
                    ->label(trans('main.employment_contract_image'))
                    ->openable()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('contract_end_date')->label(trans('main.contract_end_date'))
                    ->visible(fn(?EmploymentDuration $record)=>$record?->contract_end_date != null)
                    ->required()
                    ,
                Forms\Components\TextInput::make('contract_end_reason')->label(trans('main.contract_end_reason'))
                    ->visible(fn(?EmploymentDuration $record)=>$record?->contract_end_date != null)
                    ->required()
                    ,
                Forms\Components\TextInput::make('note')->label(trans('main.note'))
                    ->visible(fn(?EmploymentDuration $record)=>$record?->contract_end_date != null)
                    ,
                Forms\Components\FileUpload::make('attachment')
                    ->visible(fn(?EmploymentDuration $record)=>$record?->contract_end_date != null)
                    ->label(trans('main.attachment'))
                    ->openable()
                    ->columnSpanFull(),
                ]),
                // Section::make()
                // ->hidden(fn(EmploymentDuration $employmentDuration) =>$employmentDuration?->contract_end_date == null)
                // ->schema([
                //     Forms\Components\DatePicker::make('contract_end_date')->label(trans('main.contract_end_date'))->visibleOn('view'),
                //     Forms\Components\TextInput::make('contract_end_reason')->label(trans('main.contract_end_reason'))->visibleOn('view'),
                //     Forms\Components\TextInput::make('note')->label(trans('main.note'))->visibleOn('view'),
                //     Forms\Components\TextInput::make('attachment')->label(trans('main.contract_end_attachment'))->visibleOn('view'),
                // ])

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('designation_id')
            ->columns([
                Tables\Columns\TextColumn::make('department.name')->label(trans_choice('main.department',1)),
                Tables\Columns\TextColumn::make('designation.name')->label(trans_choice('main.job',1)),
                Tables\Columns\TextColumn::make('contract_start_date')->label(trans('main.the_start'))->date('Y-m-d'),
                Tables\Columns\TextColumn::make('contract_end_date')->label(trans('main.the_end'))
                        ->formatStateUsing(fn (string $state) => $state ?? trans("main.employment_duration_active"))
                        ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('contract_end_date')->label(trans('main.the_end'))
                        ->formatStateUsing(fn (string $state) => $state ?? trans("main.employment_duration_active"))
                        ->date('Y-m-d'),
                Tables\Columns\TextColumn::make('duration')->label(trans('main.duration_day'))
                        ->state(function (EmploymentDuration $duration){
                            $start = Carbon::parse($duration->contract_start_date);
                            $end = Carbon::parse($duration->contract_end_date);
                            //duration between to date in days
                            return $start->diff($end)->format('%d '.trans('main.days'));
                        } ),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->modalHeading(trans('main.add_employment_duration'))->createAnother(false),
            ])
            ->actions([
                // Tables\Actions\Action::make('download_contract_attachment')
                // ->label(trans('main.download_contract_attachment'))
                // ->icon('icon-download')
                // ->color('info')
                // ->visible(fn(EmploymentDuration $record)=>$record->contract_image != null)
                // ->action(function(EmploymentDuration $record,array $data){
                //     return response()->download('storage/'.$record->contract_image);
                // }),
                // Tables\Actions\Action::make('download_end_duration_attachment')
                // ->label(trans('main.download_end_duration_attachment'))
                // ->icon('icon-download')
                // ->color('info')
                // ->visible(fn(EmploymentDuration $record)=>$record->attachment != null)
                // ->action(function(EmploymentDuration $record,array $data){
                //     return response()->download('storage/'.$record->attachment);
                // }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                // ->visible(fn(EmploymentDuration $record)=>$record->contract_end_date == null)
                ,
                Tables\Actions\Action::make('end_duration')
                ->visible(fn(EmploymentDuration $record)=>$record->contract_end_date == null)
                ->label(trans('main.finish'))
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
