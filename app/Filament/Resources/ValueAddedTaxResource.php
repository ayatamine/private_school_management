<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SchoolSetting;
use App\Models\ValueAddedTax;
use Filament\Resources\Resource;
use Filament\Actions\StaticAction;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ValueAddedTaxResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ValueAddedTaxResource\RelationManagers;

class ValueAddedTaxResource extends Resource
{
    protected static ?string $model = ValueAddedTax::class;

    protected static ?string $navigationIcon = 'icon-vat';
    public static function getNavigationGroup():string
    {
        return trans('main.finance_settings');
    }
    // public static function canCreate():bool 
    // {
    //     return false;
    // }
    public static function getModelLabel():string
    {
        return trans_choice('main.value_added_tax',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.value_added_tax',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.value_added_tax',2);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->columns(2)
                ->schema([
                        Forms\Components\TextInput::make('name')->label(trans('main.tax_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('percentage')->label(trans('main.tax_percentage'))
                            ->required()
                            ->numeric(),
                        Forms\Components\DatePicker::make('applies_at')->label(trans('main.applies_at'))
                            ->required(),
                        Forms\Components\Toggle::make('is_saudi_student_exepmted')->label(trans('main.is_saudi_excluded'))->inline(false)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('main.tax_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('percentage')->label(trans('main.tax_percentage'))
                    ->formatStateUsing(fn (string $state) => trans("% $state"))
                    ->sortable(),
                Tables\Columns\TextColumn::make('applies_at')->label(trans('main.applies_at'))
                    ->date()
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_saudi_student_exepmted')->label(trans('main.is_saudi_excluded')),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Action::make('sendEmail')->label(trans('main.change_tax_number'))
                // ->form([
                //     Forms\Components\TextInput::make('tax_number')->required()->label(trans('main.tax_number'))
                //     ->default(SchoolSetting::first()?->added_value_tax_number),
                // ])
                // ->color('info')
                // ->action(function (array $data) {
                //     SchoolSetting::first()->update(['added_value_tax_number'=>$data['tax_number']]);
                // })
                // ->modalSubmitAction(fn (StaticAction $action) => $action->label(trans('main.confirm')))
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.value_added_tax',2)
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
            'index' => Pages\ListValueAddedTaxes::route('/'),
            'create' => Pages\CreateValueAddedTax::route('/create'),
            'edit' => Pages\EditValueAddedTax::route('/{record}/edit'),
            'update-tax' => Pages\UpdateTaxNumber::route('/update-tax'),
        ];
    }
}
