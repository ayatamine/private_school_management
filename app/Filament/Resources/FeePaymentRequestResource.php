<?php

namespace App\Filament\Resources;

use MPDF;
use NumberToWord;
use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SchoolSetting;
use App\Models\ReceiptVoucher;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FeePaymentRequestResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ReceiptVoucherResource\RelationManagers;

class FeePaymentRequestResource extends Resource
{
    protected static ?string $model = ReceiptVoucher::class;

    protected static ?string $navigationIcon = 'icon-receipt_voucher';
    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.fee_payment_requests',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.fee_payment_requests',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.fee_payment_requests',2);
    }
    public static function canCreate():bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                    Forms\Components\TextInput::make('id')->label(trans('main.id'))
                    ->formatStateUsing(fn($state) =>$state."#")
                    ->disabled()
                    ->visibleOn('view'),
                Forms\Components\Select::make('student_id')->label(trans_choice('main.student',1))
                    ->relationship('student', 'username')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Student::where('username', 'like', "%{$search}%")
                                                            ->orWhereHas('user',function($query) use ($search){
                                                                 $query->where('national_id', 'like', "%{$search}%");
                                                            })->pluck('username', 'id')->toArray())
                    ->getOptionLabelUsing(fn ($value): ?string => Student::find($value)?->username)
                    ->required(),
                Forms\Components\TextInput::make('value')->label(trans('main.value'))
                    ->required()
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $numberToWord = new NumberToWord();
                        $set('value_in_alphabetic',$numberToWord->convert($state));
                    })
                    ->live(onBlur: true),
                // Forms\Components\TextInput::make('payment_method')
                //     ->label(trans_choice('main.payment_method',1))
                //     ->default(trans('main.transfer'))
                //     // ->disabled()
                //     ->hidden(fn(Get $get) =>$get('payment_method_id') != null),
                Forms\Components\Select::make('payment_method_id')
                    ->label(trans_choice('main.payment_method',1))
                    ->relationship('paymentMethod', 'name')
                    // ->hidden(fn(Get $get) =>$get('payment_method_id') == null)
                    ,
                
                Forms\Components\TextInput::make('value_in_alphabetic')->label(trans('main.value_in_alphabetic'))
                    ->maxLength(255),
                Forms\Components\DatePicker::make('payment_date')->label(trans('main.payment_date'))
                    ->required(),
                Forms\Components\FileUpload::make('document')->label(trans('main.document')),
                Forms\Components\Textarea::make('simple_note')->label(trans('main.note'))
                        ->maxLength(255),
                Forms\Components\Select::make('status')->label(trans('main.status'))
                        ->options([
                        'pending'=>trans('main.pending'),
                        'paid'=>trans('main.paid'),
                        'rejected'=>trans('main.rejected')
                        ] ),
             
                ]),
              
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(ReceiptVoucher::whereNotNull('added_by'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(trans('main.id'))
                    ->formatStateUsing(fn($state) =>$state."#")
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.username')->label(trans_choice('main.student',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')->label(trans_choice('main.payment_method',1))
                    ->formatStateUsing(fn($state)=> $state == 'transfer' ? trans('main.transfer') : $state)
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('value')->label(trans('main.value'))
                    ->formatStateUsing(fn($state)=>  $state." ".env('DEFAULT_CURRENCY'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('value_in_alphabetic')->label(trans('main.value_in_alphabetic'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')->label(trans('main.approvel_status'))
                ->badge()
                    ->formatStateUsing(fn($state)=> trans("main.$state"))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'info',
                        'paid' => 'success',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('payment_date')->label(trans('main.payment_date'))
                    ->date()
                    ->sortable(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make()
                //     ->visible(fn(ReceiptVoucher $rv)=> $rv->status == 'pending'),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.fee_payment_requests',2)
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
            'index' => Pages\ListFeePaymentRequests::route('/'),
            'create' => Pages\CreateFeePaymentRequest::route('/create'),
            'edit' => Pages\EditFeePaymentRequest::route('/{record}/edit'),
            'view' => Pages\ViewFeePaymentRequest::route('/{record}'),
        ];
    }
}
