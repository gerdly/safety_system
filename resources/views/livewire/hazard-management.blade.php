<div class="container mx-auto px-4 sm:px-8 mt-4 mb-5">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-md-center mb-6 gap-3">
        <h2 class="text-2xl font-bold text-blue-700">Safety Management System - Reports</h2>
    </div>

    <!-- Session Alerts -->
    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 shadow-sm" role="alert">
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        
        <!-- Loading State (Handled via Livewire target) -->
        <div wire:loading wire:target="closeHazard" class="w-full p-5 text-center bg-gray-50 border-b border-gray-200">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-2 text-gray-600">Processing...</p>
        </div>

        @if($reports->isEmpty())
            <div class="p-10 text-center text-gray-500">
                <h5 class="text-lg font-medium">No hazard reports found.</h5>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            
                            <!-- Hidden on mobile, visible on medium screens and up -->
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">Company</th>
                            
                            <!-- Hidden on mobile/tablet, visible on large screens -->
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">Area</th>
                            
                            <!-- Hidden on mobile, visible on small screens and up -->
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden sm:table-cell">Reporter</th>
                            
                            <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-center md:text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                            <tr class="hover:bg-gray-50 border-b border-gray-100">
                                
                                <td class="px-5 py-4 whitespace-nowrap text-sm">
                                    <strong class="text-gray-900">SMS-{{ $report->id }}</strong>
                                </td>
                                
                                <td class="px-5 py-4 whitespace-nowrap text-sm">
                                    <span class="block text-gray-900">{{ \Carbon\Carbon::parse($report->incident_date)->format('m/d/Y') }}</span>
                                    <small class="text-gray-500 block md:hidden">{{ \Carbon\Carbon::parse($report->incident_date)->format('H:i') }}</small>
                                </td>
                                
                                <td class="px-5 py-4 text-sm text-gray-900 hidden md:table-cell">
                                    {{ $report->company->name ?? 'N/A' }}
                                </td>
                                
                                <td class="px-5 py-4 text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $report->department_area }}
                                </td>
                                
                                <td class="px-5 py-4 text-sm text-gray-900 hidden sm:table-cell">
                                    {{ empty($report->reporter_name) ? 'Anonymous' : $report->reporter_name }}
                                </td>
                                
                                <td class="px-5 py-4 whitespace-nowrap text-sm">
                                    @if (is_null($report->qaReview))
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Pending</span>
                                    @elseif (is_null($report->qaReview->actual_closure_date))
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Open</span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Closed</span>
                                    @endif
                                </td>
                                
                                <td class="px-5 py-4 whitespace-nowrap text-sm">
                                    <!-- Flexbox to stack buttons on mobile and align them horizontally on desktop -->
                                    <div class="flex flex-col md:flex-row gap-2 md:justify-end">
                                        @if (is_null($report->qaReview))
                                            <a href="{{ url('/sms/hazard-review/' . $report->id) }}" class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none w-full md:w-auto">
                                                Review
                                            </a>
                                        @else
                                            <a href="{{ url('/sms/hazard-report-print/' . $report->id) }}" class="inline-flex justify-center items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none w-full md:w-auto">
                                                View
                                            </a>

                                            @if (is_null($report->qaReview->actual_closure_date))
                                                <button wire:click="closeHazard({{ $report->id }})" wire:loading.attr="disabled" class="inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none w-full md:w-auto disabled:opacity-50">
                                                    Close
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination links -->
            <div class="px-5 py-4 bg-white border-t border-gray-200">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>