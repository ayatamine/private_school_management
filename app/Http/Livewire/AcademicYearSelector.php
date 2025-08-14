<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AcademicYearSelector extends Component
{
    public $selectedAcademicYearId;
    public $academicYears;
    public $canChangeAcademicYear;

    protected $listeners = ['setAcademicYear'];

    public function mount()
    {
        $this->academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $this->selectedAcademicYearId = session('selected_academic_year_id') ?? 
            AcademicYear::where('is_default', true)->first()?->id ??
            $this->academicYears?->first()?->id;

        $this->canChangeAcademicYear = Auth::check() && Auth::user()->hasPermission('change_academic_year');
    }

    public function setAcademicYear($academicYearId)
    {
        $this->selectedAcademicYearId = $academicYearId;
        session(['selected_academic_year_id' => $academicYearId]);
        $this->emit('academicYearChanged', $academicYearId);
    }

    public function render()
    {
        return view('livewire.academic-year-selector');
    }
}
