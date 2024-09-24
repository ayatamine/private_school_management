<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use App\Models\Transport;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TransportResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransportResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class TransportResource extends Resource
{
    protected static ?string $model = Transport::class;

    protected static ?string $navigationIcon = 'icon-student_transportation';

    public static function getNavigationGroup():string
    {
        return trans_choice('main.student',2);
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.student_transportation',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.student_transportation',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.student_transportation',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->columns(2)
                ->schema([
                Forms\Components\Select::make('student_id')->label(trans_choice('main.student',1))
                    ->relationship('student', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn (Student $record) => "{$record->first_name} {$record->middle_name} #{$record->registration_number}")
                    ->searchable(['registration_number','first_name', 'middle_name'])
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('vehicle_id')->label(trans('main.bus_name'))
                    ->relationship('vehicle', 'car_name')
                    ->required(),
                Forms\Components\Select::make('transport_fee_id')->label(trans_choice('main.transport_fee',1))
                    ->relationship('transportFee', 'name')
                    ->required(),
                Forms\Components\DatePicker::make('created_at')->label(trans( 'main.registration_date'))
            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.username')->label(trans_choice('main.student',1))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vehicle.car_name')->label(trans_choice('main.bus_name',1))
                    ->sortable(),                
                Tables\Columns\TextColumn::make('transportFee.name')->label(trans_choice( 'main.transport_fee',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('registredBy.username')->label(trans('main.registered_by'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans( 'main.registration_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label(trans( 'main.updated_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.student_transportation',2)
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
            'index' => Pages\ListTransports::route('/'),
            'create' => Pages\CreateTransport::route('/create'),
            'edit' => Pages\EditTransport::route('/{record}/edit'),
        ];
    }
}
