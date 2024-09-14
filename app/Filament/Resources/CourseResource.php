<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
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
                    ->dateTime()
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
