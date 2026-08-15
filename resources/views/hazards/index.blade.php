<!-- resources/views/hazards/index.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hazards Management') }}
        </h2>
    </x-slot>

    <!-- Inject the Livewire component. The component itself already has the max-w-7xl container and paddings -->
    <livewire:hazard-management />
</x-app-layout>