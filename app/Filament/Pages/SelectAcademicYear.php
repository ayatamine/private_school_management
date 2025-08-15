<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms; 
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use App\Models\AcademicYear;

class SelectAcademicYear extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Settings';
    

    protected static string $view = 'filament.pages.select-academic-year';

    public ?int $academic_year_id = null;
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public  function getTitle(): string
    {
        return __('main.select_default_academic_year_description');
    }
  
    public static function navigationLabel(): string
    {
        return __('main.select_academic_year');
    }
    public static function getRecordTitle(): string
    {
        return __('main.select_academic_year');
    }
    public function mount(): void
    {
        $academic_year_id = AcademicYear::where('is_global_default', true)->value('id');

        $this->form->fill([
            'academic_year_id' => $academic_year_id,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('academic_year_id')
                    ->label(trans_choice('main.academic_year',1))
                    ->options(AcademicYear::orderBy('name', 'desc')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public function save()
    {
        $data = $this->form->getState();
        $id = $data['academic_year_id'] ?? null;
   
        if (!$id) {
            Notification::make()
                ->title(__('main.please_select_academic_year'))
                ->danger()
                ->send();
            return;
        }
      
        AcademicYear::where('is_global_default', true)->update(['is_global_default' => false]);
        $academicYear = AcademicYear::find($id);
        if ($academicYear) {
            $academicYear->is_global_default = true;
            $academicYear->save();
        }

        Notification::make()
            ->title(__('main.default_academic_year_updated_successfully'))
            ->success()
            ->send();
        return redirect()->route('filament.admin.pages.dashboard');
    }
    public function return()
    {
        return redirect()->route('filament.admin.pages.dashboard');
    }
}
