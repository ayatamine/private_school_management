<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Income;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentMethod;
use Filament\Resources\Resource;
use App\Models\TransactionCategory;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\IncomeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\IncomeResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class IncomeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'icon-incomes';

    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.income',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.income',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.income',2);
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasPermissionTo('view_in_menu_income');
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_in_menu',
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
                Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('transaction_category_id')->label(trans('main.income_name'))
                        ->relationship('transactionCategory', 'name')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->label(trans('main.name'))
                                ->required()
                                ->maxLength(255),
                        ])->createOptionUsing(function (array $data): int {
                            $data['type'] = 'income';
                            return TransactionCategory::create($data)->getKey();
                        }),
                    Forms\Components\Select::make('payment_method_id')->label(trans_choice('main.payment_method',1))
                        ->relationship(
                            name: 'paymentMethod',
                            modifyQueryUsing: fn (Builder $query) => $query->latest(),
                        )
                        ->getOptionLabelFromRecordUsing(fn (PaymentMethod $record) => "{$record->name} -- {$record->financeAccount->name}")
                        ->required(),
                    Forms\Components\TextInput::make('value')->label(trans('main.value'))
                        ->required()
                        ->numeric(),
                    
                    Forms\Components\Textarea::make('note')->label(trans('main.note'))
                        ->maxLength(16777215)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('attachment')->label(trans('main.add_attachment'))
                        ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transactionCategory.name')->label(trans('main.income_name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')->label(trans_choice('main.payment_method',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')->label(trans('main.value'))
                    ->formatStateUsing(fn(string $state) =>$state." ".trans('main.'.env('DEFAULT_CURRENCY')) )
                    ->sortable(),
               
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registeredBy.username')->label(trans('main.registered_by')),
            ])
            ->filters([
                SelectFilter::make('transaction_category_id')->label(trans_choice('main.income_name',1))
                    ->relationship('transactionCategory', 'name')->searchable()
                    ->preload(),
                SelectFilter::make('payment_method_id')->label(trans_choice('main.payment_method',1))
                    ->relationship('paymentMethod', 'name')->searchable()
                    ->preload(),
                TernaryFilter::make('is_tax_included')->label(trans('main.is_tax_included'))
                    ->nullable()
                    ->attribute('is_tax_included'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(fn()=>auth()->user()->hasPermissionTo('print_income'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.income',2)
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
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
