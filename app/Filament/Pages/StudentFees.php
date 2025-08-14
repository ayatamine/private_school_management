<?php

namespace App\Filament\Pages;

use App\Models\Student;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use App\Traits\InteractWithStudentRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class StudentFees extends Page implements HasInfolists
{
    use InteractWithStudentRecord;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Student Fees';
    protected static ?string $navigationGroup = 'Students';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.student-fees';
    public Student $record;
    use InteractsWithInfolists;

    public function mount(): void
    {
        $this->record = Student::findOrFail(request('record'));
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Set to true if you want this in the main navigation
    }

    public static function getRecordTitle(): ?string
    {
        return $this->record->name; // Assuming Student has a 'name' attribute
    }

    public function infolist(Infolist $infolist): Infolist
    {
        // dd($this->record);
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make(trans_choice('main.tuition_fee', 2))
                    ->id('tuition_fee-section')
                    ->schema([
                        ViewEntry::make('tuitionFees')
                            ->label(trans_choice('main.tuition_fee', 2))
                            ->view('infolists.components.view-student-tuition-fees')
                    ]),
                Section::make(trans_choice('main.transport_fee', 2))
                    ->id('transport_fee-section')
                    ->schema([
                        TextEntry::make('transport_registration_date')
                            ->label(trans('main.transport_registeration_date'))
                            ->formatStateUsing(fn($state) => $state != '' ? $state : trans('main.not_registered_yet'))
                            ->date('Y-m-d')
                            ->weight('bold'),
                        ViewEntry::make('transportFees')
                            ->label(trans_choice('main.transport_fee', 2))
                            ->view('infolists.components.view-student-transport-fee')
                    ]),
                Section::make(trans_choice('main.general_fee', 2))
                    ->id('general_fee-section')
                    ->schema([
                        ViewEntry::make('otherFees')
                            ->label(trans_choice('main.general_fee', 2))
                            ->view('infolists.components.view-student-general-fee')
                    ]),
                Section::make(trans('main.account_ballance_actual'))
                    ->id('account_ballance_actual-section')
                    ->schema([
                            ViewEntry::make('summary')->label(trans('main.account_ballance_actual'))->view('infolists.components.total-fees-summary')
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Add any header actions here if needed
        ];
    }
}
