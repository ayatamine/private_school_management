<x-filament::page>
    <div class="space-y-6">
        {{ $this->form }}
        <x-filament::button wire:click="save" color="primary">
            {{ trans('main.change_default_academic_year') }}
        </x-filament::button>
        <x-filament::button wire:click="return" color="danger">
            {{ trans('main.return') }}
        </x-filament::button>
    </div>
</x-filament::page>
