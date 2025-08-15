<div>
    <!-- Button to open modal -->
    @if(employeeHasPermission('change_default_academic::year'))
        <a 
            href="{{ route('filament.admin.pages.select-academic-year') }}"
            class="text-sm text-gray-600 hover:text-gray-900"
        >
        @php
            $ay = \App\Models\AcademicYear::where('is_global_default', true)->first();
        @endphp
        {{ $ay?->name ?? __('main.no_default_academic_year_set') }}
        </a>
    @else
        <span class="text-sm text-gray-600 hover:text-gray-900">
            {{ __('main.no_default_academic_year_set') }}
        </span>
    @endif
</div>
