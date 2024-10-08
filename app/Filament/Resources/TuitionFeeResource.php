<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\TuitionFee;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TuitionFeeResource\Pages;
use App\Filament\Resources\TuitionFeeResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class TuitionFeeResource extends Resource
{
    protected static ?string $model = TuitionFee::class;

    protected static ?string $navigationIcon = 'icon-tuition_fees';
    public static function getNavigationGroup():string
    {
        return trans('main.fees_managment');
    }
    public static function getModelLabel():string
    {
        return trans_choice('main.tuition_fee',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.tuition_fee',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.tuition_fee',2);
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                        ->relationship('academicYear', 'name')
                        ->required(),
                    Forms\Components\Select::make('course_id')->label(trans_choice('main.academic_course',1))
                        ->relationship('course', 'name')
                        ->unique(ignoreRecord:true)
                        ->required(),
                    // Forms\Components\TextInput::make('payment_partition_count')->label(trans('main.payment_partition_count'))
                    //     ->required()
                    //     ->numeric(),
                    Repeater::make('payment_partition')->label(trans('main.payment_partition'))
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('partition_name')->label(trans('main.partition_name'))
                                    ->required(),
                                Forms\Components\TextInput::make('value')->label(trans('main.value'))
                                    ->required()
                                    ->numeric(),
                                Forms\Components\DatePicker::make('due_date')->label(trans('main.due_date'))
                                    ->required(),
                                Forms\Components\DatePicker::make('due_date_end_at')->label(trans('main.due_date_end_at'))
                                    ->required(),
                            ])
                            ->addActionLabel(trans('main.add_new_payment_partition'))
               ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                    Tables\Columns\TextColumn::make('academicYear.name')->label(trans_choice('main.academic_year',1))
                        ->sortable(),
                    Tables\Columns\TextColumn::make('course.name')->label(trans_choice('main.academic_course',1))
                        ->sortable(),
                    Tables\Columns\TextColumn::make('payment_partition_count')->label(trans_choice('main.payment_partition_count',1))
                        ->numeric()
                        ->sortable(),
                    Tables\Columns\TextColumn::make('created_at')->label(trans('main.created_at'))
                        ->date()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('updated_at')->label(trans('main.updated_at'))
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
                Tables\Actions\ReplicateAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.tuition_fee',2)
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
            'index' => Pages\ListTuitionFees::route('/'),
            'create' => Pages\CreateTuitionFee::route('/create'),
            'edit' => Pages\EditTuitionFee::route('/{record}/edit'),
        ];
    }
}
