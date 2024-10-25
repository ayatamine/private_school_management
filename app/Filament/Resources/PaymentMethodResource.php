<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentMethod;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PaymentMethodResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\PaymentMethodResource\RelationManagers;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'icon-payment_methods';
    public static function getNavigationGroup():string
    {
        return trans('main.finance_settings');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.payment_method',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.payment_method',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.payment_method',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('finance_account_id')->label(trans('main.finance_account_name'))
                    ->relationship('financeAccount', 'name')
                    ->required(),
                    Forms\Components\TextInput::make('name')->label(trans('main.payment_method_name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Toggle::make('add_refrence_number')->label(trans('main.add_refrence_number'))
                        ->columnSpanFull()
                        ->default(false)
                        ->live(),
                    Forms\Components\TextInput::make('code')->label(trans('main.reference_number'))
                        // ->visible(fn (Get $get) => $get('add_refrence_number') == true)
                        ->maxLength(255)
                        ->default('PM')
                        ->visible(fn(Get $get)=>$get('add_refrence_number')),
                    Forms\Components\Toggle::make('is_code_required')->label(trans('main.is_reference_number_required'))
                        ->columnSpanFull()
                        ->visible(fn (Get $get) => $get('add_refrence_number') == true),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('financeAccount.name')->label(trans('main.finance_account_name'))
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')->label(trans('main.payment_method_name'))
                    ->searchable(),
                // Tables\Columns\TextColumn::make('code')->label(trans('main.reference_number'))
                //     ->searchable(),
                // Tables\Columns\ToggleColumn::make('is_code_required')->label(trans('main.is_reference_number_required')),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
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
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.payment_method',2)
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
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
            'view' => Pages\ViewPaymentMethod::route('/{record}'),
        ];
    }
}
