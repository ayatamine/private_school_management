<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ConcessionFee;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ConcessionFeeResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ConcessionFeeResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ConcessionFeeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ConcessionFee::class;

    protected static ?string $navigationIcon = 'icon-concession_fees';
    public static function getNavigationGroup():string
    {
        return trans('main.fees_managment');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.concession_fee',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.concession_fee',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.concession_fee',2);
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
            
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_in_menu_concession::fee');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                        ->relationship('academicYear', 'name')
                        ->required(),
                    Forms\Components\TextInput::make('name')->label(trans('main.name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')->label(trans('main.type'))
                        ->options([
                            'percentage'=> trans('main.percentage'),
                            'value'=> trans('main.a_value'),
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('value')->label(trans('main.value'))
                        ->required()
                        ->numeric(),
                    Forms\Components\Toggle::make('is_active')->label(trans(key: 'main.activate_concession_fee'))
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('academicYear.name')->label(trans_choice('main.academic_year',1))
                        ->sortable(),
                Tables\Columns\TextColumn::make('name')->label(trans('main.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')->label(trans('main.type'))
                        ->formatStateUsing(fn (string $state) => $state == "value" ? trans("main.a_value") :  trans("main.$state"))
                        ->sortable(),
                Tables\Columns\TextColumn::make('value')->label(trans('main.value'))
                        ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label(trans(key: 'main.activate_concession_fee')),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                ->relationship('academicYear', 'name')->searchable()
                ->preload(),
                SelectFilter::make('type')->label(trans_choice('main.type',1))
                ->options([
                    'percentage'=> trans('main.percentage'),
                    'value'=> trans('main.a_value'),
                ]),
                TernaryFilter::make('is_active')->label(trans('main.activate_concession_fee'))
                    ->attribute('is_active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.concession_fee',2)
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
            'index' => Pages\ListConcessionFees::route('/'),
            'create' => Pages\CreateConcessionFee::route('/create'),
            'edit' => Pages\EditConcessionFee::route('/{record}/edit'),
        ];
    }
}
