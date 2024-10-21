<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use MPDF;
use PDF;
use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SchoolSetting;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InvoiceResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InvoiceResource\RelationManagers;

class InvoiceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'icon-invoices';
    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.invoice',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.invoice',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.invoice',2);
    }
    public static  function canCreate():bool
    {
        return false;
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'update',
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year_id',1))
                    ->relationship('academicYear', 'name')
                    ->required(),
                Forms\Components\Select::make('student_id')->label(trans('main.student'))
                    ->relationship('student', 'username')
                    ->required(),
                Forms\Components\TextInput::make('number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')->label(trans('main.serial_number'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')->label(trans('main.invoice_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('academicYear.name')->label(trans_choice('main.academic_year',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.username')->label(trans_choice('main.student',1))
                    ->url(fn (Invoice $record): string => route('filament.admin.resources.students.view', ['record' => $record->student->id]))
                    ->sortable()
                    ->openUrlInNewTab(),
                
                Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('print_invoice')
                    ->icon('icon-print')
                    ->color('info')
                    ->label(trans('main.print_invoice'))
                    ->action(function(Invoice $record) {
                        $data = ['invoice' => $record,'settings'=>SchoolSetting::first()];
                     
                        // $pdf = PDF::loadView('pdf.invoice', $data);
                        // // return $pdf->download('document.pdf');
                        // return response()->streamDownload(function () use ($pdf) {
                        //     echo $pdf->stream();
                        //     }, 'name.pdf');

                            // $html = view('pdf.invoice',$data)->toArabicHTML();

                            // $pdf = PDF::loadHTML($html)->output();
                            
                            // $headers = array(
                            //     "Content-type" => "application/pdf",
                            // );
                            
                            // // Create a stream response as a file download
                            // return response()->streamDownload(
                            //     fn () => print($pdf), // add the content to the stream
                            //     "invoice.pdf", // the name of the file/stream
                            //     $headers
                            // );

                            $pdf = MPDF::loadView('pdf.invoice', $data);
                            $pdf->simpleTables = true;

                            return $pdf->download('document.pdf');
                    })
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
