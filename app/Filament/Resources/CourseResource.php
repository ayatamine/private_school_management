<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CourseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CourseResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class CourseResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'icon-academic_courses';
    public static function getNavigationGroup():string
    {
        return trans('main.academic_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.academic_course',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.academic_course',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.academic_course',2);
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'view',
            'view_any',
            'update',
            'delete',
            
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_any_course');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')
                    ->relationship('academicYear', 'name')->label(trans_choice('main.academic_year',1))
                    ->required(),
                Forms\Components\Select::make('academic_stage_id')->label(trans_choice('main.academic_stage',1))
                    ->relationship('academicStage', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')->label(trans('main.name'))
                    ->required()
                    ->maxLength(255),
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('main.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.name')->label(trans_choice( 'main.academic_year',number: 1))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academicStage.name')->label(trans_choice( 'main.academic_stage',1))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.academic_course',2)
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
