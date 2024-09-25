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
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StudentTerminationResource\Pages;
use App\Filament\Resources\StudentTerminationResource\RelationManagers;

class StudentTerminationResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'icon-student_termination';

    public static function getNavigationGroup():string
    {
        return trans_choice('main.student',2);
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
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(trans('main.termination'))
                    ->columnSpanFull()
                    ->schema([ Grid::make()
                     ->schema([
                        Forms\Components\Select::make('student_id')->label(trans_choice('main.student',1))
                            ->options( Student::whereNull('termination_date')->selectRaw("id, concat(first_name, ' ', middle_name) as full_name")->pluck('full_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->hiddenOn('edit'),
                        Forms\Components\TextInput::make('username')->label(trans_choice('main.student',1))->hiddenOn('create')->disabled(),
                        Forms\Components\DatePicker::make('termination_date')->label(trans('main.termination_date'))->required(),
                        Forms\Components\TextArea::make('termination_reason')->label(trans('main.termination_reason'))
                            ->columnSpanFull()
                            ->maxLength(26663)->required(),
                        Forms\Components\FileUpload::make(name: 'termination_document')->label(trans('main.document'))
                            ->columnSpanFull()
                            ->directory('termination_documents'),
                        
                ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Student::query()->whereNotNull('termination_reason'))
            ->columns([
                Tables\Columns\TextColumn::make('first_name')->label(trans('main.first_name'))
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('middle_name')->label(trans('main.middle_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('termination_date')->label(trans('main.termination_date'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('termination_reason')->label(trans('main.termination_reason'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('terminatedBy.username')->label(trans('main.terminated_by'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => Pages\ListStudentTerminations::route('/'),
            'create' => Pages\CreateStudentTermination::route('/create'),
            'edit' => Pages\EditStudentTermination::route('/{record}/edit'),
        ];
    }
}
