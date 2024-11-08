<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Semester;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SemesterResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SemesterResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class SemesterResource extends Resource
{
    protected static ?string $model = Semester::class;

    protected static ?string $navigationIcon = 'icon-semesters';
    public static function getNavigationGroup():string
    {
        return trans('main.academic_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.semester',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.semester',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.semester',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                    ->relationship('academicYear', 'name')
                    ->required(),
                Forms\Components\Select::make('course_id')->label(trans_choice('main.academic_course',1))
                    ->relationship('course', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')->label(trans('main.name'))
                    ->required(),
                Forms\Components\TextInput::make('max_students_number')->label(trans_choice('main.max_students_number',1))
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_registration_active')->label(trans('main.is_registration_active'))
                    ->required(),
                Forms\Components\Toggle::make('is_promotion_active')->label(trans('main.is_promotion_active'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')->label(trans_choice('main.academic_year',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('course.name')->label(trans_choice('main.academic_course',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')->label(trans('main.name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_students_number')->label(trans('main.max_students_number'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_registration_active')->label(trans('main.is_registration_active')),
                Tables\Columns\ToggleColumn::make('is_promotion_active')->label(trans('main.is_promotion_active')),
                Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.semester',2)
                ])->disableXlsx(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSemesters::route('/'),
            'create' => Pages\CreateSemester::route('/create'),
            'edit' => Pages\EditSemester::route('/{record}/edit'),
        ];
    }
}
