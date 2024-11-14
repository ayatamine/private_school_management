<?php

namespace App\Filament\Student\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Student;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Student\Resources\StudentResource\Pages;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use App\Filament\Student\Resources\StudentResource\RelationManagers;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $isScopedToTenant = true;
    public static bool $shouldRegisterNavigation=false;
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
            ->columns([
                //
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
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                \Filament\Infolists\Components\Section::make(trans('main.radical_infos'))
                        ->headerActions([
                            Action::make(trans('main.edit'))
                                ->url(fn (Student $record): string => route('filament.admin.resources.students.edit', $record))
                        ])
                        ->columns(2)
                        ->id('main-section')
                        ->schema([
                                TextEntry::make('registration_number')->label(trans('main.registration_number'))
                                        ->formatStateUsing(fn($state)=>$state."#")
                                        ->weight(FontWeight::Bold)
                                        ->size(TextEntrySize::Large)
                                        // ->badge()
                                        ->color('danger')
                                        ->columnSpanFull(),
                                TextEntry::make('semester.academicYear.name')->label(trans_choice('main.academic_year',1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.course.academicStage.name')->label(trans_choice('main.academic_stage',1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.course.name')->label(trans_choice('main.academic_course',number: 1))->weight(FontWeight::Bold),
                                TextEntry::make('semester.name')->label(trans_choice('main.semester',number: 1))->weight(FontWeight::Bold),
                                TextEntry::make('first_name')->label(trans('main.first_name'))->weight(FontWeight::Bold),
                                TextEntry::make('middle_name')->label(trans('main.middle_name'))->weight(FontWeight::Bold),
                                TextEntry::make(name: 'third_name')->label(trans('main.third_name'))->weight(FontWeight::Bold),
                                TextEntry::make('last_name')->label(trans('main.last_name'))->weight(FontWeight::Bold),
                                TextEntry::make('nationality')->label(trans('main.nationality'))
                                ->formatStateUsing(fn($state)=>$state == "saudian" ? trans('main.saudian') : $state)
                                ->weight(FontWeight::Bold),
                                TextEntry::make('user.national_id')->label(trans('main.national_id'))->weight(FontWeight::Bold),
                                TextEntry::make('user.gender')->label(trans('main.gender'))->weight(FontWeight::Bold),
                                TextEntry::make('user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                                TextEntry::make('user.email')->label(trans('main.email'))->weight(FontWeight::Bold),
                                TextEntry::make('created_at')->label(trans('main.registration_date'))->date()->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.parent_data'))
                        ->columns(2)
                        ->id('parent-section')
                        ->schema([
                                TextEntry::make('parent.full_name')->label(trans('main.full_name'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.national_id')->label(trans('main.national_id'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.email')->label(trans('main.email'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.phone_number')->label(trans('main.phone_number'))->weight(FontWeight::Bold),
                                TextEntry::make('parent.user.gender')->label(trans('main.gender'))->weight(FontWeight::Bold),
                        ]),
                \Filament\Infolists\Components\Section::make(trans_choice('main.tuition_fee',2))
                        ->id('tuition_fee-section')
                        ->schema([

                                ViewEntry::make('tuitionFees')->label(trans_choice('main.tuition_fee',2))->view('infolists.components.view-student-tuition-fees')
                                // ->registerActions([
                                //     Action::make('editPartitions')
                                //         ->label(trans('main.edit_partitions'))
                                //         ->form([
                                //             Forms\Components\Select::make('name')
                                //                 ->required(),
                                //         ])
                                //         ->action(function (array $data, Student $record) {
                                //             $record->status()->create($data);
                                //         }),
                                // ]),
                        ]),
                \Filament\Infolists\Components\Section::make(trans_choice('main.transport_fee',2))
                        ->id('transport_fee-section')
                        ->schema([

                                ViewEntry::make('transportFees')->label(trans_choice('main.transport_fee',2))->view('infolists.components.view-student-transport-fee')
                        ]),
                \Filament\Infolists\Components\Section::make(trans_choice('main.general_fee',2))
                        ->id('general_fee-section')
                        ->schema([
                                ViewEntry::make('otherFees')->label(trans_choice('main.general_fee',2))->view('infolists.components.view-student-general-fee')
                        ]),

                \Filament\Infolists\Components\Section::make(trans('main.payments'))
                        ->id('payments-section')
                        ->headerActions([
                            Action::make('edit')
                                ->label(trans('main.new_receipt_payment'))
                                ->url(fn(STudent $student) =>route('filament.admin.resources.receipt-vouchers.create',['student'=>$student->id]))
                                ->openUrlInNewTab(),
                            Action::make('printAllPayments')
                    ->icon('icon-print')
                                ->color('info')
                                ->label(trans('main.print_all_payments'))
                                ->url(fn(Student $student)=> route('print_pdf',['type'=>"all_payments",'id'=>$student->id]))
                                ->visible(fn(Student $student)=>count($student->receiptVoucher) != 0)
                        ])
                        ->schema([
                            ViewEntry::make('receiptVoucher')->label(trans_choice('main.payments',2))->view('infolists.components.student-payments')
                       
                        ]),
                \Filament\Infolists\Components\Section::make(trans('main.financial_infos'))
                        ->columns(2)
                        ->id('financial-section')
                        ->schema([


                                TextEntry::make('opening_balance')->label(trans('main.opening_balance'))
                                ->formatStateUsing(fn (string $state) => $state." ".trans("main.".env('DEFAULT_CURRENCY')))
                                ->weight(FontWeight::Bold),
                                ViewEntry::make('finance_document')->label(trans('main.document'))->view('infolists.components.view-financial-document'),
                                TextEntry::make(name: 'note')->label(trans('main.note'))->weight(FontWeight::Bold)

                       
                        ]),
               
                \Filament\Infolists\Components\Section::make(trans('main.account_ballance'))
                        ->columns(2)
                        ->id('account_ballance-section')
                        ->schema([
                                TextEntry::make('balance')->label(trans('main.account_ballance_actual'))
                                ->color('primary')
                                ->size(TextEntry\TextEntrySize::Large)
                                ->weight(FontWeight::Bold)
                                ->formatStateUsing(fn(string $state)=>$state." "." " .trans("main.".env('DEFAULT_CURRENCY').""))
                                ->tooltip(function (TextEntry $component): ?string {
                                    
                                    return trans('main.balance_calculate_method');
                                }),
                                TextEntry::make('total_fees_after_due_date')->label(trans('main.total_fees_to_pay'))
                                ->color('primary')
                                ->size(TextEntry\TextEntrySize::Large)
                                ->weight(FontWeight::Bold)
                                ->formatStateUsing(fn(string $state)=>$state." "." " .trans("main.".env('DEFAULT_CURRENCY').""))
                                ->tooltip(function (TextEntry $component): ?string {
                                    
                                    return trans('main.total_fees_to_pay_method');
                                }),
                                TextEntry::make('total_fees_rest')->label(trans('main.total_fees_rest'))
                                ->color('primary')
                                ->size(TextEntry\TextEntrySize::Large)
                                ->weight(FontWeight::Bold)
                                ->formatStateUsing(fn(string $state)=>$state." "." " .trans("main.".env('DEFAULT_CURRENCY').""))
                                ->tooltip(function (TextEntry $component): ?string {
                                    
                                    return trans('main.total_fees_to_rest_method');
                                })
                       
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'view' => Pages\ViewMyDetails::route('/{record}'),
        ];
    }
}
