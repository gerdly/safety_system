<!-- resources/views/dashboard.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Empty state for our future data tables -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-4">
                    Welcome, {{ auth()->user()->name }}!
                </h3>
                {{-- <p class="text-gray-600">
                    Selecciona una opción del menú lateral para comenzar.
                </p> --}}
                
                @if(auth()->user()->role === 'superadmin')
                    <div class="mt-4 p-4 bg-blue-50 text-blue-700 rounded-md border border-blue-200">
                        <strong>Administrator Mode:</strong>Have full access to manage users, hazards, and system settings.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>