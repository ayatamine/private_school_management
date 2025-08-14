<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;

class ChangeDefaultAcademicYear extends Component
{
    public $selectedAcademicYearId;
    public $academicYears;
    public $showModal = false;

    protected $listeners = ['openModal'];

    public function mount()
    {
        $this->academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $this->selectedAcademicYearId = AcademicYear::where('is_default', true)->first()?->id;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function changeDefault()
    {
        if (!$this->selectedAcademicYearId) {
            $this->addError('selectedAcademicYearId', trans('main.please_select_academic_year'));
            return;
        }

        try {
            AcademicYear::where('is_default', true)->update(['is_default' => false]);
            AcademicYear::find($this->selectedAcademicYearId)->update(['is_default' => true]);
            
            $this->showModal = false;
            $this->emit('academicYearChanged', $this->selectedAcademicYearId);
            $this->emit('alert', [
                'type' => 'success',
                'message' => trans('main.default_academic_year_updated_successfully')
            ]);
        } catch (\Exception $e) {
            $this->emit('alert', [
                'type' => 'error',
                'message' => trans('main.error_updating_default_academic_year')
            ]);
        }
    }

    public function render()
    {
        return view('livewire.change-default-academic-year');
    }
}
