<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use App\Models\Transport;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
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
                    ->relationship('student', 'first_name',
                        modifyQueryUsing: fn (Builder $query) => $query->doesntHave('transport')
                    )
                    ->getOptionLabelFromRecordUsing(fn (Student $record) => "{$record->first_name} {$record->middle_name} #{$record->registration_number}")
                    ->searchable(['registration_number','first_name', 'middle_name'])
                    ->preload()
                    ->required()
                    ->disabled(request()->is('admin/transports/*/edit')),
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
            // ->query(Transport::query()->whereNull('termination_reason'))
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
                Tables\Columns\TextColumn::make('termination_reason')->label(trans('main.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state == null ? trans("main.transportation_active")  : trans("main.transportation_inactive"))
                    ->color(fn (string $state) => $state == null ? trans("success")  : trans("danger"))
                    ,
                // Tables\Columns\TextColumn::make('updated_at')->label(trans( 'main.updated_at'))
                //     ->date()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // SelectFilter::make('termination_reason')->label(trans('main.status'))->options([
                //     'male' => trans('main.transportation_active'),
                //     'female' => trans('main.transportation_inactive'),
                // ]),
                TernaryFilter::make('terminated_at')->label(trans('main.status'))
                    ->nullable()
                    ->trueLabel(trans('main.transportation_active'))
                    ->falseLabel(trans('main.transportation_inactive'))
                    ->attribute('termination_date')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('termination_date'),
                        false: fn (Builder $query) => $query->whereNotNull('termination_date'),
                    )->default(true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->hidden(fn (Transport $record) => $record->termination_reason != null),
                Tables\Actions\EditAction::make()->hidden(fn (Transport $record) => $record->termination_reason != null),
                Action::make(trans('main.restore'))
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(trans('main.restore_student_transportation'))
                ->modalDescription(trans('main.restore_student_transportation_description'))
                ->action(function (Transport $record) {
                  
                   $record->update([
                        'terminated_by'=>null,
                        'termination_date'=>null,
                        'termination_reason'=>null,
                        'termination_document'=>null,
                   ]);
                   Notification::make()
                                       ->title(trans('main.student_restored_success'))
                                       ->icon('heroicon-o-document-text')
                                       ->iconColor('success')
                                       ->send();
                })->hidden(fn (Transport $record) => $record->termination_reason == null),
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
