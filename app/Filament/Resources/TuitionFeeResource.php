<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\TuitionFee;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TuitionFeeResource\Pages;
use App\Filament\Resources\TuitionFeeResource\RelationManagers;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class TuitionFeeResource extends Resource implements HasShieldPermissions
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
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super-admin') || (employeeHasPermission('view_in_menu_tuition::fee'));
    }
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_in_menu',
            'create',
            'view',
            'view_any',
            'update',
            'delete',
            'replicate',
            
        ];
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
                        ->live()
                        ->required(),
                    Forms\Components\Select::make('course_id')->label(trans_choice('main.academic_course',1))
                        ->options(fn (Get $get): Collection => Course::query()
                        ->where('academic_year_id', $get('academic_year_id'))
                        ->whereDoesntHave('tuitionFee')
                        ->pluck('name', 'id'))
                        ->rules([
                            fn (TuitionFee $fee,Get $get ): Closure => function (string $attribute, $value, Closure $fail) use($fee,$get) {
                                if (TuitionFee::whereCourseId($value)->whereAcademicYearId($get('academic_year_id'))->whereNot('id',$fee->id)->first()) {
                                    $fail(trans('main.cannot_add_same_course_for_current_year'));
                                }
                            },
                        ])
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
                Tables\Actions\ReplicateAction::make()
                ->visible(auth()->user()->hasPermissionTo('replicate_tuition::fee'))
                ->successRedirectUrl(fn (TuitionFee $replica): string => route('filament.admin.resources.tuition-fees.edit', [
                    'record' => $replica,
                ])),
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
