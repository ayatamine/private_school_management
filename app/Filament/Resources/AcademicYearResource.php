<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AcademicYearResource\Pages;
use App\Filament\Resources\AcademicYearResource\RelationManagers;
use App\Models\AcademicYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class AcademicYearResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'icon-academic_years';
    public static function getNavigationGroup():string
    {
        return trans('main.academic_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.academic_year',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.academic_year',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.academic_year',2);
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_in_menu',
            'create',
            'view',
            'view_any',
            'update',
            'delete',
            'print',
            
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_in_menu_academic::year');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label(trans('main.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date')->label(trans('main.start_date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')->label(trans('main.end_date'))
                    ->required(),
                Forms\Components\TextInput::make('description')->label(trans('main.description'))
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_default')->label(trans('main.is_default'))
                    ->required(),
                Forms\Components\Toggle::make('is_registration_active')->label(trans('main.is_registration_active'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('main.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')->label(trans('main.start_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')->label(trans('main.end_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')->label(trans('main.description'))->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_default')->label(trans('main.is_default'))
                    ->boolean(),
                Tables\Columns\ToggleColumn::make('is_registration_active')->label(trans('main.is_registration_active')),
                Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(fn()=>employeeHasPermission('print_academic::year'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.academic_year',2)
                ])->disableXlsx(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ;
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
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }
}
