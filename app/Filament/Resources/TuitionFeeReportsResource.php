<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TuitionFeeReportsResource\Pages;
use App\Filament\Resources\TuitionFeeReportsResource\RelationManagers;
use App\Models\Student;
use App\Models\TuitionFeeReports;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TuitionFeeReportsResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'icon-reports';

    public static function getNavigationGroup():string
    {
        return trans('main.finance');
    }
   
   
    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false;
    // }
    public static function getModelLabel():string
    {
        return trans_choice('main.tuition_fee_reports',1);
    }
    public static function getNavigationLabel():string
    {
        return trans_choice('main.tuition_fee_reports',2);
    }

    public static function getPluralModelLabel():string
    {
        return trans_choice('main.tuition_fee_reports',2);
    }

    public static function canCreate():bool
    {
        return false;
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Student::where('status','approved'))
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')->label(trans('main.registration_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')->label(trans('main.username'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.name')->label(trans_choice('main.semester',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.full_name')->label(trans_choice('main.parent',1))
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.phone_number')->label(trans('main.phone_number'))
                    ->searchable(),
               
                Tables\Columns\TextColumn::make('total_fees')->label(trans('main.total_fees'))
                    ->getStateUsing(function(Student $record) {
                        // return whatever you need to show
                        return $record->calculatePaymentPartitions('App\Models\TuitionFee',"tuitionFees");
                    }),
                Tables\Columns\TextColumn::make('total_fees_discounts')->label(trans('main.total_fees_discounts'))
                    ->getStateUsing(function(Student $record) {
                        // return whatever you need to show
                        return $record->calculateFeesDiscounts('App\Models\TuitionFee',"tuitionFees");
                    }),
                Tables\Columns\TextColumn::make('total_paid_fees')->label(trans('main.total_paid_fees'))
                    ->getStateUsing(function(Student $record) {
                        // return whatever you need to show
                        // return $record->calculateFeesDiscounts('App\Models\TuitionFee',"tuitionFees");
                    }),
                Tables\Columns\TextColumn::make('approved_at')->label(trans('main.approved_at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTuitionFeeReports::route('/'),
            'create' => Pages\CreateTuitionFeeReports::route('/create'),
            'edit' => Pages\EditTuitionFeeReports::route('/{record}/edit'),
        ];
    }
}
