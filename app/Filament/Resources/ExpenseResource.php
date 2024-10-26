<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Expense;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentMethod;
use Filament\Resources\Resource;
use App\Models\TransactionCategory;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\ExpenseResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'icon-expenses';
    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.expense',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.expense',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.expense',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('transaction_category_id')->label(trans('main.expense_name'))
                        ->relationship('transactionCategory', 'name')
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->label(trans('main.name'))
                                ->required()
                                ->maxLength(255),
                        ])->createOptionUsing(function (array $data): int {
                            $data['type'] = 'expense';
                            return TransactionCategory::create($data)->getKey();
                        }),
                    Forms\Components\Select::make('payment_method_id')->label(trans_choice('main.payment_method',1))
                        // ->relationship('paymentMethod', 'name')
                        ->relationship(
                            name: 'paymentMethod',
                            modifyQueryUsing: fn (Builder $query) => $query->latest(),
                        )
                        ->getOptionLabelFromRecordUsing(fn (PaymentMethod $record) => "{$record->name} -- {$record->financeAccount->name}")
                        ->required(),
                    Forms\Components\TextInput::make('value')->label(trans('main.value'))
                        ->required()
                        ->numeric(),
                    Forms\Components\Toggle::make('is_tax_included')->label(trans('main.is_tax_included'))
                        ->columnSpanFull()
                        ->inline()
                        ->required(),
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
                Tables\Columns\TextColumn::make('transactionCategory.name')->label(trans('main.expense_name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')->label(trans_choice('main.payment_method',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')->label(trans('main.value'))
                    ->formatStateUsing(fn(string $state) =>$state." ".trans('main.'.env('DEFAULT_CURRENCY')) )
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_tax_included')->label(trans('main.is_tax_included'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registeredBy.username')->label(trans('main.registered_by')),
                Tables\Columns\TextColumn::make('total')->label(trans('main.total'))
                ->state(function (Expense $record): float {
                    $vat = \App\Models\ValueAddedTax::first();
                    return floatval((($vat->percentage / 100) * ($record->value)) + $record->value);
                })
            ])
            ->filters([
                SelectFilter::make('transaction_category_id')->label(trans_choice('main.expense_name',1))
                    ->relationship('transactionCategory', 'name')->searchable()
                    ->preload(),
                SelectFilter::make('payment_method_id')->label(trans_choice('main.payment_method',1))
                    ->relationship('paymentMethod', 'name')->searchable()
                    ->preload(),
                TernaryFilter::make('is_tax_included')->label(trans('main.is_tax_included'))
                    ->nullable()
                    ->attribute('is_tax_included'),
                Filter::make('created_at')
                ->label(trans('main.date_filter'))
                    ->indicator('date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label(trans('main.date_from')),
                        Forms\Components\DatePicker::make('created_until')->label(trans('main.date_to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        if (!$data['created_from'] && !$data['created_until']) {
                            return [];
                        }
                        $indicators = [];
 
                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make(trans('main.date_from') . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }
                 
                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make(trans('main.date_to') . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }
                 
                        return $indicators;
                      
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.expense',2)
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
