<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use App\Models\Transport;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TransportResource;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransportTerminationResource\Pages;
use App\Filament\Resources\TransportTerminationResource\RelationManagers;

class TransportTerminationResource extends Resource
{
    protected static ?string $model = Transport::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getNavigationGroup():string
    {
        return trans_choice('main.student',2);
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.student',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.transport_termination',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.transport_termination',2);
    }
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make(trans('main.termination'))
                ->columnSpanFull()
                ->schema([ Grid::make()
                 ->schema([
                    Forms\Components\Select::make('student_id')->label(trans_choice('main.student',1))
                                ->relationship('student', 'username',
                                modifyQueryUsing: fn (Builder $query) => $query->Has('transport')
                            )
                        // ->searchable()
                        ->preload()
                        ->required()
                        ->hiddenOn('edit'),
                    Forms\Components\TextInput::make('username')->label(trans_choice('main.student',1))->hiddenOn('create')->disabled(),
                    Forms\Components\DatePicker::make('termination_date')->label(trans('main.termination_date'))->required(),
                    Forms\Components\Textarea::make('termination_reason')->label(trans('main.termination_reason'))
                        ->columnSpanFull()
                        ->maxLength(26663)->required(),
                    
            ])
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Transport::query()->whereNotNull('termination_reason'))
            ->columns([
                Tables\Columns\TextColumn::make('student.username')->label(trans('main.name'))
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('student.middle_name')->label(trans('main.middle_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('termination_date')->label(trans('main.termination_date'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('termination_reason')->label(trans('main.termination_reason'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('terminatedBy.username')->label(trans('main.terminated_by'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                
            ])
            ->actions([
                // Action::make(trans('main.view'))
                // ->icon('icon-eye')
                // ->color('info')
                // ->url(fn(Transport $record)=> TransportResource::getUrl('view',[$record])),
                // Tables\Actions\EditAction::make(),
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
                }),
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
            'index' => Pages\ListTransportTerminations::route('/'),
            'create' => Pages\CreateTransportTermination::route('/create'),
            'edit' => Pages\EditTransportTermination::route('/{record}/edit'),
        ];
    }
}
