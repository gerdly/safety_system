<!-- Light theme sidebar configuration (Simplified) -->
<div class="h-full w-full bg-white flex flex-col">
    <!-- Header/Logo area of the sidebar -->
    <div class="p-6 flex items-center justify-center border-b border-gray-200 h-16">
        <img src="{{asset('img/sms-logo.png')}}" class="object-cover object-center h-12"   alt="">
    </div>
    
    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
        <!-- Dashboard Link -->
        <a href="{{ route('dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-50 text-gray-700 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-500' : '' }}">
            Dashboard
        </a>
        
        <!-- Hazards Management Link -->
          <a href="{{ route('hazards.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-50 text-gray-700 {{ request()->routeIs('hazards.index') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-500' : '' }}">
            Hazards Management
        </a>

        <!-- User Management Link (Visible ONLY to superadmin) -->
        @if(auth()->user()->role === 'superadmin')
            <a href="{{ route('users.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-50 text-gray-700 {{ request()->routeIs('users.index') ? 'bg-blue-50 text-blue-700 font-semibold border-l-4 border-blue-500' : '' }}">
                User Management
            </a>
        @endif
    </nav>
</div>