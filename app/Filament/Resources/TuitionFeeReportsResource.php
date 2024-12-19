<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\TuitionFeeReports;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TuitionFeeReportsResource\Pages;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\TuitionFeeReportsResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;

class TuitionFeeReportsResource extends Resource implements HasShieldPermissions
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
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super-admin') || (employeeHasPermission('view_any_tuition::fee::reports'));
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'print',
        ];
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
                        return $record->calculatePaymentPartitions('App\Models\TuitionFee',"tuitionFees");
                    }),
                Tables\Columns\TextColumn::make('total_fees_discounts')->label(trans('main.total_fees_discounts'))
                    ->getStateUsing(function(Student $record) {
                        return $record->calculateFeesDiscounts('App\Models\TuitionFee',"tuitionFees");
                    }),
                Tables\Columns\TextColumn::make('total_paid_fees')->label(trans('main.total_paid_fees'))
                    ->getStateUsing(function(Student $record) {
                        return $record->payments()    ;
                }),
                Tables\Columns\TextColumn::make('approved_at')->label(trans('main.approved_at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('academic_year_id')->label(trans_choice('main.academic_year',1))
                    ->relationship('semester.academicYear', 'name')->searchable()
                    ->preload(),
                SelectFilter::make('semester_id')->label(trans_choice('main.semester',1))
                    ->relationship('semester', 'name')->searchable()
                    ->preload(),
                
                SelectFilter::make('gender')->label(trans('main.gender'))->options([
                    'male' => trans('main.male'),
                    'female' => trans('main.female'),
                ]),
                // Filter::make('created_at')
                // ->form([
                //     TextInput::make('from')->numeric()->label(trans('main.fees_from')),
                //     TextInput::make('to')->numeric()->label(trans('main.fees_to')),
                // ])
                // ->query(function (Builder $query, array $data): Builder {
                //     return $query
                //         ->when(
                //             $data['to'],
                //             fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                //         )
                //         ->when(
                //             $data['created_until'],
                //             fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                //         );
                // })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')->label(trans('main.print'))->color('info')
                ->visible(employeeHasPermission('print_tuition::fee::reports'))
                ->extraViewData([
                    'table_header' => trans('main.menu').' '.trans_choice('main.tuition_fee_reports',2)
                ])->disableXlsx(),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make(trans('main.radical_infos'))
                        ->headerActions([
                          
                        ])
                        ->columns(2)
                        ->id('main-section')
                        ->schema([
                                TextEntry::make('first_name')->label(trans('main.first_name'))->weight(FontWeight::Bold),
                                TextEntry::make('middle_name')->label(trans('main.middle_name'))->weight(FontWeight::Bold),
                                TextEntry::make(name: 'third_name')->label(trans('main.third_name'))->weight(FontWeight::Bold),
                                TextEntry::make('last_name')->label(trans('main.last_name'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.full_name')->label(trans('main.parent'))->weight(FontWeight::Bold),
                                TextEntry::make('semester.academicYear.name')->label(trans_choice('main.academic_year',1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.course.academicStage.name')->label(trans_choice('main.academic_stage',1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.course.name')->label(trans_choice('main.academic_course',number: 1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.name')->label(trans_choice('main.semester',number: 1))->weight(FontWeight::Bold),
                                TextEntry::make('nationality')->label(trans('main.nationality'))->weight(FontWeight::Bold),
                                TextEntry::make('user.national_id')->label(trans('main.national_id'))->weight(FontWeight::Bold),
                                TextEntry::make('user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans_choice('main.tuition_fee',2))
                        ->id('tuition_fee-section')
                        ->schema([

                                ViewEntry::make('tuitionFees')->label(trans_choice('main.tuition_fee',2))->view('infolists.components.view-student-tuition-fees-reports')

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
            'view' => Pages\ViewTuitionFeeReport::route('/{record}'),
        ];
    }
}
