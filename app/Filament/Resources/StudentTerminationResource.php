<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\StudentTermination;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentTerminationResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\StudentTerminationResource\RelationManagers;

class StudentTerminationResource extends Resource implements HasShieldPermissions 
{
    protected static ?string $model = StudentTermination::class;

    protected static ?string $navigationIcon = 'icon-student_termination';

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
        return trans_choice('main.termination',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.termination',2);
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_student_termination',
            'view_any_student_termination',
            'create_student_termination',
            'update_student_termination',
            'restore_student_termination',
            'terminate_student_private',
            'print_student_termination'
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_any_student_termination_student::termination');
    }
    public static function canCreate(): bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(trans('main.termination'))
                    ->columnSpanFull()
                    ->schema([ Grid::make()
                     ->schema([
                        Forms\Components\Select::make('student_id')->label(trans_choice('main.student',1))
                            ->options( Student::whereDoesntHave('termination')->selectRaw("id, concat(first_name, ' ', middle_name) as full_name")->pluck('full_name', 'id'))
                            // ->searchable()
                            ->preload()
                            ->required()
                            ->hiddenOn(['edit', 'view']),
                        Forms\Components\TextInput::make('student.username')->label(trans_choice('main.student',1))
                            ->hiddenOn('create')
                            ->disabled(),
                        Forms\Components\DatePicker::make('termination_date')->label(trans('main.termination_date'))->required(),
                        Forms\Components\Textarea::make('termination_reason')->label(trans('main.termination_reason'))
                            ->columnSpanFull()
                            ->maxLength(26663)->required(),
                        Forms\Components\FileUpload::make(name: 'termination_document')->label(trans('main.document'))
                            ->columnSpanFull()
                            ->openable()
                            ->directory('termination_documents'),
                        
                ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->query(StudentTermination::query())
            ->columns([
                Tables\Columns\TextColumn::make('student.registration_number')->label(trans('main.registration_number'))
                    ->searchable('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.username')->label(trans('main.name'))
                ->searchable(['first_name','last_name'])
                ->sortable(),
                Tables\Columns\TextColumn::make('student.user.national_id')->label(trans('main.national_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.nationality')->label(trans('main.nationality'))
                    ->formatStateUsing(fn (string $state) => $state == 'saudian' ? trans("main.$state") : $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('course_enrolled')->label(trans('main.course_enrolled'))
                    ->state(fn (StudentTermination $student_termination) => $student_termination?->semester?->academicYear?->name .' '.$student_termination?->semester?->course?->name)                    ,
                Tables\Columns\TextColumn::make('termination_date')->label(trans('main.termination_date'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('termination_reason')->label(trans('main.termination_reason'))
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Action::make(trans('main.view'))
                // ->icon('icon-eye')
                // ->color('info')
                // ->url(fn(Student $record)=> StudentResource::getUrl('view',[$record])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make(trans('main.restore'))
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(trans('main.restore_student'))
                ->modalDescription(trans('main.restore_student_description'))
                ->visible(employeeHasPermission('restore_student'))
                ->action(function (StudentTermination $record) {
                  
                   $record->student()->update(['semester_id' => $record->terminated_semester_id]);
                   $record->delete();
                   Notification::make()
                                       ->title(trans('main.student_restored_success'))
                                       ->icon('heroicon-o-document-text')
                                       ->iconColor('success')
                                       ->send();
                }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListStudentTerminations::route('/'),
            'create' => Pages\CreateStudentTermination::route('/create'),
            'edit' => Pages\EditStudentTermination::route('/{record}/edit'),
            'view' => Pages\ViewStudentTermination::route('/{record}'),
        ];
    }
}
