<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Transfer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\FinanceAccount;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TransferResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransferResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class TransferResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Transfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
   
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'print',
        ];
    }
    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false;
    // }
    public static function getModelLabel():string
    {
        return trans_choice('main.transfer_operation',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.transfer_operation',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.transfer_operation',2);
    }
    public static function getnavigationParentItem():string
    {
        return trans_choice('main.finance_account',2);
    }
    public static function canCreate():bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Forms\Components\Section::make()
                ->schema([
                Forms\Components\Select::make('from_account_id')->label(trans('main.from_account_id'))
                    ->options(FinanceAccount::whereIsActive(true)->pluck('name','id'))
                    ->live()
                    ->required(),
                    Forms\Components\Select::make('to_account_id')->label(trans('main.to_account_id'))
                    ->options(fn (Get $get): array =>    FinanceAccount::whereIsActive(true)->whereNot('id',$get('from_account_id'))->pluck('name','id')->toArray())
                    ->required(),
                Forms\Components\TextInput::make('amount')->label(trans('main.amount'))
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('transfer_date')->label(trans('main.date'))
                    ->required(),
                Forms\Components\Textarea::make('note')->label(trans('main.note'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(trans('main.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('fromAccount.name')->label(trans('main.from_account_id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('toAccount.name')->label(trans('main.to_account_id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')->label(trans('main.amount'))
                    ->formatStateUsing(fn(string $state) =>number_format($state, 2, '.', ',')." ".trans('main.'.env('DEFAULT_CURRENCY')) )
                    ->sortable(),
                Tables\Columns\TextColumn::make('transfer_date')->label(trans('main.date'))
                    ->date(),
                Tables\Columns\TextColumn::make('registeredBy.username')->label(trans('main.username'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('cancel')
                ->label(fn(Transfer $record )=> $record->is_cancelled == true ?  trans('main.activate_operation') :  trans('main.cancel_operation')  )
                ->color(fn(Transfer $record )=> $record->is_cancelled == true ? "success" : "danger"  )
                ->requiresConfirmation()              
                ->action(function(Transfer $transfer,array $data): void {
                    $original_account = FinanceAccount::findOrFail($transfer->from_account_id);
                    $target_account = FinanceAccount::findOrFail($transfer->to_account_id);
                    
                    
                    try{
                        DB::beginTransaction();
                        if($transfer->is_cancelled == false)
                        {
                            $original_account->balance+=$transfer->amount;
                            $original_account->save();
                            $target_account->balance-=$transfer->amount;
                            $target_account->save();
                            $transfer->is_cancelled=true;
                            $transfer->save();
                            DB::commit();
                            Notification::make()
                                        ->title(trans('main.transfer_operation_cancel_success'))
                                        ->icon('heroicon-o-document-text')
                                        ->iconColor('success')
                                        ->send();
                            
                        }
                        else
                        {
                            $original_account->balance-=$transfer->amount;
                            $original_account->save();
                            $target_account->balance+=$transfer->amount;
                            $target_account->save();
                            $transfer->is_cancelled=false;
                            $transfer->save();
                            DB::commit();
                            Notification::make()
                                        ->title(trans('main.transfer_operation_activate_success'))
                                        ->icon('heroicon-o-document-text')
                                        ->iconColor('success')
                                        ->send();
                        }
                       
            
                    }
                    catch(\Exception $ex)
                    {
                        DB::rollBack();
                        throw $ex;
                    }

                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                ->action(function(Transfer $transfer,array $data): void {
                    $original_account = FinanceAccount::findOrFail($transfer->from_account_id);
                    $target_account = FinanceAccount::findOrFail($transfer->to_account_id);
                    //return money to the original account
                    try{
                        DB::beginTransaction();
                        if($transfer->is_cancelled == false)
                        {
                            $original_account->balance+=$transfer->amount;
                            $original_account->save();
                            $target_account->balance-=$transfer->amount;
                            $target_account->save();

                           
                          
                            
                        }
                         $transfer->delete();
                         DB::commit();
            
                    }
                    catch(\Exception $ex)
                    {
                        dd($ex);
                        DB::rollBack();
                        throw $ex;
                    }

                }),
            ])
            
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(auth()->user()->can('print_transfer') || employeeHasPermission('print_transfer'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.transfer_operation',2)
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
            'index' => Pages\ListTransfers::route('/'),
            'create' => Pages\CreateTransfer::route('/create'),
            'edit' => Pages\EditTransfer::route('/{record}/edit'),
        ];
    }
}
