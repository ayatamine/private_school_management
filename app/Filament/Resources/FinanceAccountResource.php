<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FinanceAccount;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FinanceAccountResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\FinanceAccountResource\RelationManagers;

class FinanceAccountResource extends Resource
{
    protected static ?string $model = FinanceAccount::class;

    protected static ?string $navigationIcon = 'icon-finance_accounts';
    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.finance_account',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.finance_account',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.finance_account',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                    Forms\Components\TextInput::make('name')->label(trans('main.finance_account_name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('type')->label(trans('main.bank_type'))
                        ->options([
                            'bank' =>trans('main.bank'),
                            'cash' =>trans('main.cash')
                        ])
                        ->live()
                        ->required(),
                    
                    Forms\Components\TextInput::make('bank_name')->label(trans('main.bank_name'))
                        ->visible(fn (Get $get) => $get('type') == 'bank' )
                        ->required(),
                    Forms\Components\TextInput::make('account_number')->label(trans('main.account_number'))
                        ->visible(fn (Get $get) => $get('type') == 'bank' )
                        ->required(),
                    Forms\Components\TextInput::make('opening_balance')->label(trans('main.opening_balance'))
                        ->numeric()
                        ->required(),
                    Forms\Components\Toggle::make('link_with_employee_payments')->label(trans('main.link_with_employee_payments'))->columnSpanFull(),
                    Forms\Components\Toggle::make('is_active')->label(trans('main.is_account_active'))->columnSpanFull(),
                    Forms\Components\Toggle::make('is_visible')->label(trans('main.is_account_visible'))->columnSpanFull(),
                ])
            ]
        );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(trans('main.finance_account_name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')->label(trans('main.bank_type'))
                    ->formatStateUsing(fn (string $state) => trans("main.$state"))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_name')->label(trans('main.bank_name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('account_number')->label(trans('main.account_number'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('link_with_employee_payments')->label(trans('main.link_with_employee_payments')),
                Tables\Columns\ToggleColumn::make('is_active')->label(trans('main.is_account_active')),
                Tables\Columns\ToggleColumn::make('is_visible')->label(trans('main.is_account_visible')),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label(trans(key: 'main.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.finance_account',2)
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
            'index' => Pages\ListFinanceAccounts::route('/'),
            'create' => Pages\CreateFinanceAccount::route('/create'),
            'edit' => Pages\EditFinanceAccount::route('/{record}/edit'),
        ];
    }
}
