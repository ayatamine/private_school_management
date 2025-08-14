<div>
    <!-- Button to open modal -->
    <button 
        wire:click="openModal" 
        class="text-sm text-gray-600 hover:text-gray-900"
    >
        {{ $academicYears->find($selectedAcademicYearId)?->name ?? trans('main.no_default_academic_year_set') }}
    </button>

    <!-- Modal -->
    <x-filament::modal 
        wire:model.live="showModal"
        :heading="trans('main.change_default_academic_year')"
        :description="trans('main.select_default_academic_year_description')"
    >
        <div class="space-y-4">
            <x-filament::select
                wire:model.live="selectedAcademicYearId"
                :label="trans('main.academic_year')"
                :options="$academicYears->pluck('name', 'id')"
                placeholder="{{ trans('main.select_academic_year') }}"
                required
            />
        </div>

        <x-slot name="actions">
            <x-filament::button 
                type="button"
                color="gray"
                wire:click="showModal = false"
            >
                {{ trans('main.cancel') }}
            </x-filament::button>

            <x-filament::button 
                type="button"
                color="primary"
                wire:click="changeDefault"
                wire:loading.attr="disabled"
            >
                {{ trans('main.save') }}
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>
