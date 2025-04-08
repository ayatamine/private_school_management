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
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\TransportResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransportResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class TransportResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Transport::class;

    protected static ?string $navigationIcon = 'icon-student_transportation';

    public static function getNavigationGroup():string
    {
        return trans('main.student_settings');
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
    public static function shouldRegisterNavigation(): bool
    {
        return employeeHasPermission('view_any_transport');
    }
    public static function canCreate(): bool
    {
        return employeeHasPermission('create_transport_registeration_transport');
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create_transport_registeration',
            'update',
            // 'delete',
            'print',
            'terminate_transport_registeration',
            
        ];
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
                    ->relationship('vehicle', 'car_name', modifyQueryUsing: fn (Builder $query,?Model $record) => $query->where('is_active', true)->orWhere('id', $record?->vehicle_id))
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
                Tables\Columns\TextColumn::make('student.registration_number')->label(trans('main.id_number'))
                    ->searchable('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.username')->label(trans_choice('main.student',1))
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('student', function ($q) use ($search) {
                            $q->where('first_name','like','%'.$search.'%')
                            ->orWhere('last_name','like','%'.$search.'%');
                        });
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.user.national_id')->label(trans('main.national_id'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.course_enrolled')->label(trans('main.course_enrolled'))
                    ->state(fn (Transport $transport) => $transport?->student?->semester?->academicYear?->name .' '.$transport?->student?->semester?->course?->name)  ,
                Tables\Columns\TextColumn::make('vehicle.car_name')->label(trans_choice('main.bus_name',1))
                    ->sortable(),                
                Tables\Columns\TextColumn::make('transportFee.name')->label(trans_choice( 'main.transport_fee',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label(trans( 'main.registration_date'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('termination_reason')->label(trans('main.status'))
                ->state(function (Transport $record) {
                    return empty($record->termination_date) 
                        ? trans('main.active_state') 
                        : trans('main.transportation_inactive');
                }) ,
                // Tables\Columns\TextColumn::make('updated_at')->label(trans( 'main.updated_at'))
                //     ->date()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn ($record) => null)
            ->filters([
                //bus name
                SelectFilter::make('vehicle_id')->label(trans_choice('main.bus_name',1))
                    ->relationship('vehicle', 'car_name'),
                //transport_fees 
                SelectFilter::make('transport_fee_id')->label(trans_choice('main.transport_fee',1))
                    ->relationship('transportFee', 'name'),
                //status
                TernaryFilter::make('termination_date')->label(trans('main.status'))
                    ->nullable()
                    ->trueLabel(trans('main.transportation_active'))
                    ->falseLabel(trans('main.transportation_inactive'))
                    ->attribute('termination_date')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNull('termination_date'),
                        false: fn (Builder $query) => $query->whereNotNull('termination_date'),
                    ),
            ])
            ->deferFilters()
            ->filtersApplyAction(
                fn (\Filament\Tables\Actions\Action $action) => $action
                    ->label(trans('main.apply')),
            )
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Action::make(trans('main.restore'))
                // ->color('success')
                // ->requiresConfirmation()
                // ->modalHeading(trans('main.restore_student_transportation'))
                // ->modalDescription(trans('main.restore_student_transportation_description'))
                // ->action(function (Transport $record) {
                  
                //    $record->update([
                //         'terminated_by'=>null,
                //         'termination_date'=>null,
                //         'termination_reason'=>null,
                //         'termination_document'=>null,
                //    ]);
                //    Notification::make()
                //                        ->title(trans('main.student_restored_success'))
                //                        ->icon('heroicon-o-document-text')
                //                        ->iconColor('success')
                //                        ->send();
                // })->hidden(fn (Transport $record) => $record->termination_reason == null),
                // Tables\Actions\DeleteAction::make(),
                
            ])
            ->bulkActions([
                // FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                // ->visible( employeeHasPermission('print_transport'))
                // ->extraViewData([
                //     'table_header' => trans('main.menu').' '.trans_choice('main.student_transportation',2)
                // ])->disableXlsx(),
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
