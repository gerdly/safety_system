<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8">
    
    <!-- Header with SMS Logo -->
    <div class="sm:mx-auto sm:w-full sm:max-w-md flex flex-col items-center mb-8">
        <img src="{{ asset('img/sms-logo.png') }}" class="h-20 w-auto object-contain mb-4" alt="SMS Logo">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">
            Safety Hazard Report
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            See something, say something. Help us maintain a safe environment.
        </p>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-lg rounded-xl sm:px-10 border border-gray-100">
            
            @if($report_submitted)
                <!-- Success State -->
                <div class="text-center py-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Report Submitted Successfully</h3>
                    <p class="mt-2 text-sm text-gray-500">Thank you for your commitment to safety. Our QA team will review this immediately.</p>
                    <div class="mt-6">
                        <button wire:click="$set('report_submitted', false)" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Another Report
                        </button>
                    </div>
                </div>
            @else
                <!-- Reporting Form -->
                <form wire:submit="submitReport" class="space-y-6">
                    
                    <!-- Company Selection -->
                    <div>
                        <label for="company_id" class="block text-sm font-medium text-gray-700">Company / Station <span class="text-red-500">*</span></label>
                        <select wire:model="company_id" id="company_id" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">-- Select a Location --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department / Area -->
                    <div>
                        <label for="department_area" class="block text-sm font-medium text-gray-700">Department / Area <span class="text-red-500">*</span></label>
                        <input wire:model="department_area" type="text" id="department_area" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="e.g. Hangar B, Main Ramp...">
                        @error('department_area') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Incident Date -->
                    <div>
                        <label for="incident_date" class="block text-sm font-medium text-gray-700">Date of Observation <span class="text-red-500">*</span></label>
                        <input wire:model="incident_date" type="date" id="incident_date" max="{{ now()->format('Y-m-d') }}" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        @error('incident_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Hazard Description -->
                    <div>
                        <label for="employee_hazard_description" class="block text-sm font-medium text-gray-700">Hazard Description <span class="text-red-500">*</span></label>
                        <textarea wire:model="employee_hazard_description" id="employee_hazard_description" rows="4" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Please describe exactly what you observed..."></textarea>
                        @error('employee_hazard_description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Suggested Mitigation -->
                    <div>
                        <label for="suggested_mitigation" class="block text-sm font-medium text-gray-700">Suggested Solution (Optional)</label>
                        <textarea wire:model="suggested_mitigation" id="suggested_mitigation" rows="2" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="How do you think this can be fixed?"></textarea>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Anonymous Logic -->
                    <div class="flex items-center">
                        <input wire:model.live="is_anonymous" id="is_anonymous" type="checkbox" class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_anonymous" class="ml-2 block text-sm text-gray-900 font-medium">
                            Submit Anonymously
                        </label>
                    </div>

                    <!-- Reporter Name (Hides if anonymous is checked) -->
                    @if(!$is_anonymous)
                        <div class="transition-all duration-300 ease-in-out">
                            <label for="reporter_name" class="block text-sm font-medium text-gray-700">Your Name</label>
                            <input wire:model="reporter_name" type="text" id="reporter_name" class="mt-1 appearance-none block w-full px-3 py-3 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="John Doe">
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-md shadow-sm text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Submit Hazard Report
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </div>
</div>