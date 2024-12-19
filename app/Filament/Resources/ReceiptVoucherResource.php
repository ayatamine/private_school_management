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
use App\Models\PaymentMethod;
use App\Models\SchoolSetting;
use App\Models\ReceiptVoucher;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReceiptVoucherResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\ReceiptVoucherResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

class ReceiptVoucherResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ReceiptVoucher::class;

    protected static ?string $navigationIcon = 'icon-receipt_voucher';
    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.receipt_voucher',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.receipt_voucher',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.receipt_voucher',2);
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            // 'delete_any',
            'print',
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_any_receipt::voucher');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->schema([
                Forms\Components\TextInput::make('id')->label(trans('main.id'))
                    ->disabledOn('view')
                    ->visibleOn('view')
                    ->maxLength(255),
                Forms\Components\Select::make('student_id')->label(trans_choice('main.student',1))
                    ->relationship('student', 'username')
                    ->preload()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => Student::where('username', 'like', "%{$search}%")
                                                            ->orWhereHas('user',function($query) use ($search){
                                                                 $query->where('national_id', 'like', "%{$search}%");
                                                            })
                                                            ->pluck('username', 'id')->toArray())
                    ->required()
                    ->default(request()['student']),
                Forms\Components\TextInput::make('value')->label(trans('main.value'))
                    ->required()
                    ->numeric()
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
                        modifyQueryUsing: fn (Builder $query) => $query->latest(),
                    )
                    ->live()
                    ->getOptionLabelFromRecordUsing(fn (PaymentMethod $record) => "{$record->name} -- {$record->financeAccount->name}")
                    ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="h-5 w-5" wire:loading wire:target="data.payment_method_id" />')))
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
                Forms\Components\Textarea::make('reject_note')->label(trans('main.reject_note'))
                        ->disabled()
                        ->visible(fn(ReceiptVoucher $receiptVoucher)=>isset($receiptVoucher->reject_note)),
             
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(ReceiptVoucher::whereNull('added_by'))
            ->columns([
                Tables\Columns\TextColumn::make('id')->label(trans_choice('main.id',1))
                    ->formatStateUsing(fn($state)=>$state."#")
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('registeredBy.username')->label(trans('main.registered_by'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('reject_note')->label(trans('main.reject_note'))
                    ->state(fn(ReceiptVoucher $receiptVoucher)=>isset($receiptVoucher->reject_note) ? $receiptVoucher->reject_note : "/" ),
                
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
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                // Action::make('status')
                //     ->color('primary')
                //     ->icon('heroicon-m-check')
                //     ->label(trans('main.change_receipt_status'))
                //     ->form([
                //        Forms\Components\Select::make('status')
                //        ->label(trans('main.status'))
                //        ->required()
                //        ->options([
                //         'paid'=>trans('main.paid'),
                //         'rejected'=>trans('main.rejected'),
                //        ])
                //     ])
                //     ->action(function(array $data,ReceiptVoucher $record) {
                //         $record->update(['status'=>$data['status']]);
                // }),
                Action::make('print_receipt_voucher')
                    ->icon('icon-print')
                    ->color('info')
                    ->label(trans('main.print_receipt_voucher'))
                    ->visible(employeeHasPermission('print_receipt::voucher'))
                    ->url(fn(ReceiptVoucher $record) => route('print_pdf',['type'=>"receipt_voucher",'id'=>$record->id]))
                    // ->action(function(ReceiptVoucher $record) {
                    //     $data = ['receipt' => $record,'settings'=>SchoolSetting::first()];
                     
                        // $pdf = PDF::loadView('pdf.receipt_voucher', $data);
                        // // return $pdf->download('document.pdf');
                        // return response()->streamDownload(function () use ($pdf) {
                        //     echo $pdf->stream();
                        //     }, 'name.pdf');

                            // $html = view('pdf.receipt_voucher',$data)->toArabicHTML();

                            // $pdf = PDF::loadHTML($html)->output();
                            
                            // $headers = array(
                            //     "Content-type" => "application/pdf",
                            // );
                            
                            // // Create a stream response as a file download
                            // return response()->streamDownload(
                            //     fn () => print($pdf), // add the content to the stream
                            //     "receipt_voucher.pdf", // the name of the file/stream
                            //     $headers
                            // );

                    //         $pdf = MPDF::loadView('pdf.receipt_voucher', $data);
                    //         $pdf->simpleTables = true;

                    //         $pdf->download("fee_payment_receipt_$record->id.pdf");
                    //         header("Refresh:0");

                    // }),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(employeeHasPermission('print_receipt::voucher'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.receipt_voucher',2)
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
            'index' => Pages\ListReceiptVouchers::route('/'),
            'create' => Pages\CreateReceiptVoucher::route('/create'),
            'edit' => Pages\EditReceiptVoucher::route('/{record}/edit'),
            'view' => Pages\ViewReceiptVoucher::route('/{record}'),
        ];
    }
}
