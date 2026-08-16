<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8">
    
    <!-- Header with System Logo and Title -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md flex flex-col items-center mb-6">
        <img src="{{ asset('img/sms-logo.png') }}" class="h-20 w-auto object-contain mb-4" alt="System Logo">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
            Hazard Report Form SMS-1
        </h2>
        
        <!-- Privacy Disclaimer Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 shadow-sm w-full">
            <p class="text-sm text-blue-800 font-medium mb-3">
                When you submit this form, it will not automatically collect your details like name and email address unless you provide it yourself.
            </p>
            <hr class="border-blue-200 mb-3">
            <p class="text-sm text-blue-800 font-medium">
                Al enviar este formulario, no se recopilarán automáticamente sus datos como nombre y dirección de correo electrónico, a menos que usted mismo los proporcione.
            </p>
        </div>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-lg rounded-xl sm:px-10 border border-gray-100">
            
            @if($report_submitted)
                <!-- Success State Container -->
                <div class="text-center py-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Report Submitted Successfully</h3>
                    <p class="mt-2 text-sm text-gray-500">Thank you for your commitment to safety.</p>
                    <div class="mt-6">
                        <button wire:click="$set('report_submitted', false)" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Another Report
                        </button>
                    </div>
                </div>
            @else
                <!-- Bilingual Reporting Form -->
                <form wire:submit="submitReport" class="space-y-6">
                    
                    <!-- 1. Reporter Name (Optional) -->
                    <div>
                        <label for="reporter_name" class="block text-sm font-semibold text-gray-800">
                            1. Name / Nombre <span class="font-normal text-gray-500 text-xs ml-1">(optional / opcional)</span>
                        </label>
                        <input wire:model="reporter_name" type="text" id="reporter_name" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>

                    <!-- 2. Company Selection -->
                    <div>
                        <label for="company_id" class="block text-sm font-semibold text-gray-800">
                            2. Company / Compañía <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="company_id" id="company_id" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">-- Select your answer / Seleccione su respuesta --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 3. Incident Date -->
                    <div>
                        <label for="incident_date" class="block text-sm font-semibold text-gray-800">
                            3. Date / Fecha <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="incident_date" type="date" id="incident_date" max="{{ now()->format('Y-m-d') }}" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('incident_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 4. Department / Area (Now Optional) -->
                    <div>
                        <label for="department_area" class="block text-sm font-semibold text-gray-800">
                            4. Department/Area
                        </label>
                        <input wire:model="department_area" type="text" id="department_area" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('department_area') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 5. Hazard Description -->
                    <div>
                        <label for="employee_hazard_description" class="block text-sm font-semibold text-gray-800">
                            5. Hazard description / Descripción del Riesgo <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="employee_hazard_description" id="employee_hazard_description" rows="4" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                        @error('employee_hazard_description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- 6. Suggested Mitigation -->
                    <div>
                        <label for="suggested_mitigation" class="block text-sm font-semibold text-gray-800">
                            6. Suggested Mitigation / Mitigación Sugerida
                        </label>
                        <textarea wire:model="suggested_mitigation" id="suggested_mitigation" rows="3" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                        @error('suggested_mitigation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Submit Button Action -->
                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-md shadow-sm text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Submit / Enviar
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>