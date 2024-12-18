<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\AcademicStage;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AcademicStageResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\AcademicStageResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class AcademicStageResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AcademicStage::class;

    protected static ?string $navigationIcon = 'icon-academic_stage';
    public static function getNavigationGroup():string
    {
        return trans('main.academic_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.academic_stage',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.academic_stage',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.academic_stage',2);
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
        return employeeHasPermission('view_in_menu_academic::stage');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label(trans(key: 'main.name'))
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
                Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(fn()=>employeeHasPermission('print_academic::stage'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.academic_stage',2)
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
            'index' => Pages\ListAcademicStages::route('/'),
            'create' => Pages\CreateAcademicStage::route('/create'),
            'edit' => Pages\EditAcademicStage::route('/{record}/edit'),
        ];
    }
}
