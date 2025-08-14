@if($academicYears && $academicYears->count() > 0)
<div class="flex items-center space-x-2">
    <span class="text-sm text-gray-600">{{ trans('main.academic_year') }}:</span>
    @if($canChangeAcademicYear)
        <select wire:model="selectedAcademicYearId" 
                wire:change="setAcademicYear($event.target.value)"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            @foreach($academicYears as $year)
                <option value="{{ $year->id }}">{{ $year->name }}</option>
            @endforeach
        </select>
    @else
        <span class="text-sm font-medium">{{ $academicYears->find($selectedAcademicYearId)?->name ?? '' }}</span>
    @endif
</div>
@endif
