<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
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
                    ->sortable(),
                
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
