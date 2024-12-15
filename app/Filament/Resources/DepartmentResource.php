<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Department;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\DepartmentResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class DepartmentResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'icon-department';
    public static function getNavigationGroup():string
    {
        return trans('main.human_resource');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.department',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.department',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.department',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label(trans('main.department_name'))
                    ->required(),
            ]);
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_in_menu',
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasPermissionTo('view_in_menu_department');
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('main.department_name'))
                    ->sortable(),
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
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans('main.department_jobs')
                ])
                ->disableXlsx()
                ,
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
