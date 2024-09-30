<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceiptVoucherResource\Pages;
use App\Filament\Resources\ReceiptVoucherResource\RelationManagers;
use App\Models\ReceiptVoucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceiptVoucherResource extends Resource
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
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('student_id')
                    ->relationship('student', 'id')
                    ->required(),
                Forms\Components\Select::make('payment_method_id')
                    ->relationship('paymentMethod', 'name'),
                Forms\Components\TextInput::make('registered_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('value_in_alphabetic')
                    ->maxLength(255),
                Forms\Components\TextInput::make('document')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_approved')
                    ->required(),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registered_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value_in_alphabetic')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListReceiptVouchers::route('/'),
            'create' => Pages\CreateReceiptVoucher::route('/create'),
            'edit' => Pages\EditReceiptVoucher::route('/{record}/edit'),
        ];
    }
}
