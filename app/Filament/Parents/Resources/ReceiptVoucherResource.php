<?php

namespace App\Filament\Parents\Resources;

use NumberToWord;
use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PaymentMethod;
use App\Models\ReceiptVoucher;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Parents\Resources\ReceiptVoucherResource\Pages;
use App\Filament\Parents\Resources\ReceiptVoucherResource\RelationManagers;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
class ReceiptVoucherResource extends Resource
{
    protected static ?string $model = ReceiptVoucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $isScopedToTenant = false;
    public static bool $shouldRegisterNavigation=false;

    public static function getModelLabel():string
    {
        return trans_choice('main.receipt_voucher',1);
    }
    public static function canCreate():bool
    {
        return true;
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.receipt_voucher',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.receipt_voucher',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                Forms\Components\Select::make('student_id')->label(trans_choice('main.student',1))
                    ->options(Student::where('parent_id', auth()->user()?->parent?->id)->pluck('username','id'))
                    ->required()
                    ->default(request()['student']),
                Forms\Components\TextInput::make('value')->label(trans('main.value'))
                    ->required()
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        $numberToWord = new NumberToWord();
                        $set('value_in_alphabetic',$numberToWord->convert($state));
                    })
                    ->live(onBlur: true)
                    ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="data.value" />'))),
                // Forms\Components\TextInput::make('payment_method')
                //     ->label(trans_choice('main.payment_method',1))
                //     ->default(trans('main.transfer'))
                //     // ->disabled()
                //     ->hidden(fn(Get $get) =>$get('payment_method_id') != null),
                Forms\Components\Select::make('payment_method_id')
                    ->label(trans_choice('main.payment_method',1))
                    ->relationship(
                        name: 'paymentMethod',
                        modifyQueryUsing: fn (Builder $query) => $query->where('is_active_for_students_and_parents',true)->latest(),
                    )
                    ->live()
                    ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="data.payment_method_id" />')))
                    ->getOptionLabelFromRecordUsing(fn (PaymentMethod $record) => "{$record->name} -- {$record->financeAccount->name}")
                    // ->hidden(fn(Get $get) =>$get('payment_method_id') == null)
                    ,
                
                Forms\Components\TextInput::make('refrence_number')->label(trans('main.refrence_number'))
                    ->hidden(function(Get $get){
                        if(!$get('payment_method_id')) return true;
                        $payment_method = PaymentMethod::find($get('payment_method_id'));
                        return !$payment_method?->is_code_required ?? true ;
                    })
                    ->maxLength(255),
                Forms\Components\TextInput::make('value_in_alphabetic')->label(trans('main.value_in_alphabetic'))
                    ->maxLength(255),
                Forms\Components\DatePicker::make('payment_date')->label(trans('main.payment_date'))
                    ->required(),
                Forms\Components\FileUpload::make('document')->label(trans('main.document')),
                Forms\Components\Textarea::make('simple_note')->label(trans('main.note'))
                        ->maxLength(255),
             
                ])
            ]);
    }


    public static function table(Table $table): Table
    {

        return $table
            ->query(ReceiptVoucher::whereHas('student',function($query){
                    $query->whereParentId(auth()->user()?->parent?->id);
            }))
            ->columns([
                Tables\Columns\TextColumn::make('student.registration_number')->label(trans_choice('main.registration_number',1))
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
                Tables\Columns\TextColumn::make('reject_note')->label(trans('main.reject_note'))
                    ->visible(fn(ReceiptVoucher $receiptVoucher)=>isset($receiptVoucher->reject_note)),
                Tables\Columns\TextColumn::make('registeredBy.username')->label(trans('main.registered_by'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                   ->label(trans('main.status'))
                   ->options([
                    'pending'=>trans('main.pending'),
                    'paid'=>trans('main.paid'),
                    'rejected'=>trans('main.rejected'),
                   ])
                   
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('print_receipt_voucher')
                    ->icon('icon-print')
                    ->visible(fn(ReceiptVoucher $receiptVoucher) => $receiptVoucher->status == "paid")
                    ->color('info')
                    ->label(trans('main.print_receipt_voucher'))
                    ->url(fn(ReceiptVoucher $record) => route('print_pdf',['type'=>"receipt_voucher",'id'=>$record->id]))
            ])
            ->bulkActions([
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
            'index' => Pages\ListReceiptVouchers::route('/'),
            'create' => Pages\CreateReceiptVoucher::route('/create'),
            'edit' => Pages\EditReceiptVoucher::route('/{record}/edit'),
            'view' => Pages\ViewReceiptVoucher::route('/{record}'),
        ];
    }
}