<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        
        <!-- Header and Search Bar -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Hazards Management</h2>
            
            <div class="w-full max-w-md">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <!-- Search Icon -->
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <!-- Livewire model binding with a 300ms debounce to prevent excessive database queries -->
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition duration-150 ease-in-out" 
                        placeholder="Search by department, description or reporter..."
                    >
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Incident Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporter</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Loop through the paginated hazards collection -->
                        @forelse ($hazards as $hazard)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $hazard->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($hazard->incident_date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $hazard->department_area }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <!-- Anonymous Reporting Logic applied in the UI -->
                                    @if($hazard->reporter_name)
                                        <span class="text-gray-900 font-medium">{{ $hazard->reporter_name }}</span>
                                    @else
                                        <span class="text-gray-400 italic">Anonymous</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <!-- Dynamic Risk Badge based on QA Review existence and score -->
                                    @if($hazard->qaReview)
                                        @php
                                            $score = $hazard->qaReview->risk_score;
                                            
                                            // Determine badge color based on matrix values
                                            if ($score >= 15) {
                                                $badgeClass = 'bg-red-100 text-red-800';
                                                $riskLabel = 'High';
                                            } elseif ($score >= 8) {
                                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                                $riskLabel = 'Medium';
                                            } else {
                                                $badgeClass = 'bg-green-100 text-green-800';
                                                $riskLabel = 'Low';
                                            }
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                            {{ $riskLabel }} ({{ $score }})
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">
                                            Pending QA
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-blue-600 hover:text-blue-900 font-semibold focus:outline-none">
                                        View details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <!-- Empty state if search returns no results -->
                            <tr>
                                <td colspan="6" class="px-6 py-10 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p>No hazard reports found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $hazards->links() }}
            </div>
        </div>
    </div>
</div>