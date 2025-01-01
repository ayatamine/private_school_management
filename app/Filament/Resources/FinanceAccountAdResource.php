<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Transfer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FinanceAccount;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TransferResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FinanceAccountAdResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\FinanceAccountAdResource\RelationManagers;

class FinanceAccountAdResource extends Resource
{
    protected static ?string $model = FinanceAccount::class;

    protected static ?string $navigationIcon = 'icon-finance_accounts';
    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
   
    public static function canCreate(): bool
    {
        return false;
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.finance_account_main',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.finance_account_main',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.finance_account_main',2);
    }
    public static function getnavigationParentItem():string
    {
        return trans_choice('main.finance_account',2);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_finance_account_ad');
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
            ->query(FinanceAccount::whereIsVisible(true))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(trans('main.finance_account_id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')->label(trans('main.finance_account_name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')->label(trans('main.type'))
                    ->formatStateUsing(fn (string $state) => trans("main.$state"))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('paymentMethods')->label(trans_choice('main.payment_method',2))
                    ->state(fn (FinanceAccount $account): string => implode(', ', $account->paymentMethods()->pluck('name')->all()))
                    ,
                Tables\Columns\TextColumn::make('balance')->label(trans('main.balance'))
                    ->formatStateUsing(fn (string $state) => number_format($state, 2, '.', ',') .' '.trans('main.'.env("DEFAULT_CURRENCY"))),
                
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
            ->headerActions([


            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(fn()=>employeeHasPermission('print_finance::account'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.finance_account',2)
                ])->disableXlsx(),
                
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make(trans('main.radical_infos'))
                        ->headerActions([
                          
                        ])
                        ->id('main-section')
                        ->schema([
                            ViewEntry::make('')
                            ->view('infolists.components.view-account-changes')                
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.balance'))
                        ->columns(2)
                        ->id('balance-section')
                        ->schema([
                                TextEntry::make('total_incomes')->label(trans('main.total_incomes'))
                                ->state(fn(FinanceAccount $account) =>$account->totalIncomes())
                                ->weight(FontWeight::Bold),
                                TextEntry::make('totalExpenses')->label(trans('main.totalExpenses'))
                                ->state(fn(FinanceAccount $account) =>$account->totalExpenses())
                                ->weight(FontWeight::Bold),
                                TextEntry::make('totalPayment')->label(trans('main.totalPayment'))
                                ->state(fn(FinanceAccount $account) =>$account->totalPayment())
                                ->weight(FontWeight::Bold),
                                TextEntry::make('opening_balance')->label(trans('main.opening_balance'))->weight(FontWeight::Bold),
                                TextEntry::make('balance')->label(trans('main.balance'))->weight(FontWeight::Bold),
                            // ViewEntry::make('')
                            // ->view('infolists.components.view-finance-accou')                
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
            'index' => Pages\ListFinanceAccountAds::route('/'),
            'create' => Pages\CreateFinanceAccountAd::route('/create'),
            'edit' => Pages\EditFinanceAccountAd::route('/{record}/edit'),
            'view' => Pages\ViewFinanceAccountAd::route('/{record}'),
        ];
    }
}
