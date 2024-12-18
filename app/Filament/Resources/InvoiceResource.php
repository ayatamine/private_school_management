<?php

namespace App\Filament\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use MPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use ArPHP\I18N\Arabic;
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
            'view_in_menu',
            'view',
            'view_any',
            // 'update',
            'print'
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_in_menu_invoice');
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
                    ->visible(fn()=>employeeHasPermission('print_invoice'))
                    ->url(fn(Invoice $record) => route('print_pdf',['type'=>"invoice",'id'=>$record->id]))
                   
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
