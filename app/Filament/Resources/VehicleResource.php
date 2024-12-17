<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Vehicle;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VehicleResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VehicleResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class VehicleResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'icon-vehicle';
    public static function getNavigationGroup():string
    {
        return trans('main.transportation');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.vehicle',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.vehicle',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.vehicle',2);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super-admin') || (auth()->user()?->employee && auth()->user()?->employee->hasPermissionTo('view_in_menu_vehicle'));
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
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->columns(2)
                ->schema([
                Forms\Components\TextInput::make('car_name')->label(trans('main.car_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('plate_number')->label(trans('main.plate_number'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('form_number')->label(trans('main.form_number'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('expire_date')->label(trans('main.expire_date'))
                    ->required(),
                Forms\Components\TextInput::make('insurance_name')->label(trans('main.insurance_name'))
                    ->required(),
                Forms\Components\DatePicker::make('insurance_expire_at')->label(trans('main.insurance_expire_at'))
                    ->required(),
                Forms\Components\DatePicker::make('periodic_inspection_expire_at')->label(trans('main.periodic_inspection_expire_at'))
                    ->required(),
                Forms\Components\FileUpload::make('documents')->label(trans('main.documents'))->multiple()->columnSpanFull()->preserveFilenames()->directory('vehicles'),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('car_name')->label(trans('main.car_name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('plate_number')->label(trans('main.plate_number'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('form_number')->label(trans('main.form_number'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('expire_date')->label(trans('main.expire_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurance_name')->label(trans('main.insurance_name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('insurance_expire_at')->label(trans('main.insurance_expire_at'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('periodic_inspection_expire_at')->label(trans('main.periodic_inspection_expire_at'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label(trans(key: 'main.updated_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                ->visible(auth()->user()?->employee && auth()->user()?->employee->hasPermissionTo('print_vehicle'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.vehicle',2)
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
