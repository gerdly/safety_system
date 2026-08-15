<!-- Container view for the User Management module -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <!-- Inject the Livewire component -->
    <livewire:user-management />
</x-app-layout>