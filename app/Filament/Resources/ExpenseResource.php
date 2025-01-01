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
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\ExpenseResource\Pages;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ExpenseResource extends Resource implements HasShieldPermissions
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
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_any_expense');
    }
    public static function getPermissionPrefixes(): array
    {
        return [
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
                Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('id')->label(trans('main.id_number'))
                        ->default(Expense::latest()->first()->id + 1)
                        ->dehydrated()
                        ->disabled(),
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
                    Forms\Components\DatePicker::make('expensed_date')->label(trans('main.expensed_date'))
                        ->required(),
                    Forms\Components\Toggle::make('is_tax_included')->label(trans('main.is_tax_included'))
                        ->columnSpanFull()
                        ->inline()
                        ->required(),
                    Forms\Components\Textarea::make('note')->label(trans('main.note'))
                        ->maxLength(16777215)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('attachment')->label(trans('main.add_attachment'))
                        ->directory('expenses')
                        ->columnSpanFull(),
                    Forms\Components\TextArea::make('cancel_reason')->label(trans('main.cancel_reason'))
                        ->visible(fn (Expense $record) => $record->cancel_reason != null)
                        ->dehydrated()
                        ->hiddenOn(['create','edit'])
                        ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Expense::orderBy('expensed_date','desc'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(trans('main.id_number'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('transactionCategory.name')->label(trans('main.item_name'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    // ->numeric(2,',',',')
                    // ->formatStateUsing(fn(string $state) =>number_format($state, 2, '.', ',')." ".trans('main.'.env('DEFAULT_CURRENCY')) )
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_tax_included')->label(trans('main.tax'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('paymentMethod.name')->label(trans('main.payment'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.financeAccount.name')->label(trans('main.account'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('expensed_date')->label(trans('main.expensed_date'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.creation'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_cancelled')->label(trans('main.status'))
                    ->formatStateUsing(fn (string $state) => $state == true ? trans('main.cancelled') : trans('main.active'))
                    ->color(fn (string $state) => $state == true ? "danger" : "success"),
                Tables\Columns\TextColumn::make('registeredBy.username')->label(trans('main.username')),
                // Tables\Columns\TextColumn::make('total')->label(trans('main.total'))
                // ->state(function (Expense $record): float {
                //     $vat = \App\Models\ValueAddedTax::first();
                //     return floatval((($vat->percentage / 100) * ($record->value)) + $record->value);
                // })
                Tables\Columns\TextColumn::make('value')->label(trans('main.value'))
                ->summarize(Summarizer::make()->label(trans('main.total_only'))->label(trans('main.total_only'))
                ->using(function(\Illuminate\Database\Query\Builder $query): string {
                   $vat = \App\Models\ValueAddedTax::latest()->first();
                   $total =0;
                   $url =url()->current();
                   $parsedUrl = parse_url($url);
               
                   $date_from = $queryParams['tableFilters']['created_at']['created_from'] ?? null;
                   $date_to = $queryParams['tableFilters']['created_at']['created_until'] ?? null;
                   $payment_method_id = $queryParams['tableFilters']['payment_method_id']['value'] ?? null;
                   $is_tax_included = $queryParams['tableFilters']['is_tax_included']['value'] ?? null;
                   $transaction_category_id = $queryParams['tableFilters']['transaction_category_id']['value'] ?? null;
                 if(array_key_exists('query',$parsedUrl))    parse_str($parsedUrl['query'], $queryParams);
                    $expenses = Expense::where('is_cancelled',false)
                    ->when(
                        $date_from, // Check if $date_from is not null or empty
                        fn ($query) => $query->whereDate('created_at', '>=', $date_from),
                    )
                    ->when(
                        $date_to, // Check if $date_to is not null or empty
                        fn ($query) => $query->whereDate('created_at', '<=', $date_to),
                    )
                    ->when(
                        $payment_method_id, // Check if $date_to is not null or empty
                        fn ($query) => $query->wherePaymentMethodId($payment_method_id),
                    )
                    ->when(
                        $is_tax_included == 1, // Check if $date_to is not null or empty
                        fn ($query) => $query->whereIsTaxIncluded(true),
                    )
                   ->get();
                   foreach($expenses as $exp)
                   {
                        $value = floatval(str_replace(',', '', $exp->value));
                    //    if($exp->is_tax_included) {
                    //        if($vat->created_at > $exp->created_at)  $vat = \App\Models\ValueAddedTax::whereDate('created_at','<',$exp->created_at)->first() ?? $vat;
                    //        $total+=floatval((($vat->percentage / 100) * $value) + $value);
                    //    }else
                    //    {
                           $total+=$value;
                    //    }
                   }
                   return $total;
                } )->numeric(
                    2,',',','
               ))->suffix(' '.trans('main.'.env('DEFAULT_CURRENCY')))

            ])
            ->filters([
                SelectFilter::make('transaction_category_id')->label(trans_choice('main.expense_name',1))
                    ->relationship('transactionCategory', titleAttribute: 'name')
                    ->preload(),
                SelectFilter::make('payment_method_id')->label(trans_choice('main.payment_method',1))
                    ->relationship('paymentMethod', 'name')
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
            ->deferFilters()
            ->filtersApplyAction(
                fn (\Filament\Tables\Actions\Action $action) => $action
                    ->label(trans('main.apply')),
            )
            ->actions([
                Tables\Actions\Action::make('cancel')
                ->label(fn(Expense $record )=> $record->is_cancelled == true ?  trans('main.activate') :  trans('main.cancel')  )
                ->color(fn(Expense $record )=> $record->is_cancelled == true ? "success" : "danger"  )
                ->requiresConfirmation()  
                ->form([
                    Forms\Components\TextInput::make('cancel_reason')
                    ->visible(fn(Expense $record )=> $record->is_cancelled == false)
                    ->label(trans('main.cancel_reason')),
                ])              
                ->action(function(Expense $expense,array $data): void {
                   
                     $expense->is_cancelled = !$expense->is_cancelled;
                     $expense->cancel_reason = isset($data['cancel_reason']) ? $data['cancel_reason'] : null;
                     $expense->save();
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(fn()=>employeeHasPermission('print_expense'))
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
