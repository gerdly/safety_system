<div> <!-- ELEMENTO RAÍZ DE LIVEWIRE -->

    <!-- Jetstream Page Header Slot -->
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Hazard Review <span class="text-gray-400 font-normal">/ SMS-{{ $report->id }}</span>
            </h2>
            
            <!-- Ruta de escape UX -->
            <a href="{{ route('hazards.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                &larr; Back to Hazards Management
            </a>
        </div>
    </x-slot>
    <!-- Wrapper utilizing Alpine.js to manage the offcanvas guide state -->
    <div x-data="{ openGuide: false }" class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        
        @if ($report->qaReview)
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded shadow-sm">
                <h4 class="text-blue-800 font-bold text-lg">Review Completed</h4>
                <p class="text-blue-700">This hazard report has already been reviewed and processed.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Left Column: Read-Only Employee Report Data (Span 5 columns) -->
                <div class="lg:col-span-5 flex flex-col">
                    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden h-full">
                        <div class="bg-blue-700 text-white px-5 py-3">
                            <h5 class="text-lg font-semibold m-0">Original Report #{{ $report->id }}</h5>
                        </div>
                        <div class="p-5 space-y-3 text-sm text-gray-700">
                            <p><strong class="font-bold text-gray-900">Date Reported:</strong> {{ \Carbon\Carbon::parse($report->submitted_date)->format('m/d/Y g:i A') }}</p>
                            <p><strong class="font-bold text-gray-900">Incident Date:</strong> {{ \Carbon\Carbon::parse($report->incident_date)->format('m/d/Y') }}</p>
                            <p><strong class="font-bold text-gray-900">Company:</strong> {{ $report->company->name ?? 'N/A' }}</p>
                            <p><strong class="font-bold text-gray-900">Department/Area:</strong> {{ $report->department_area ?? 'N/A' }}</p>
                            <p><strong class="font-bold text-gray-900">Reporter:</strong> {{ empty($report->reporter_name) ? 'Anonymous' : $report->reporter_name }}</p>
                            
                    
                            
                            <div>
                                <strong class="font-bold text-gray-900 block mb-1">Hazard Description:</strong>
                                <p class="bg-gray-50 p-3 rounded border border-gray-100">{{ $report->employee_hazard_description }}</p>
                            </div>
                            
                            @if (!empty($report->suggested_mitigation))
                                <div>
                                    <strong class="font-bold text-gray-900 block mb-1">Suggested Mitigation:</strong>
                                    <p class="bg-gray-50 p-3 rounded border border-gray-100">{{ $report->suggested_mitigation }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column: QA Analyst Review Form (Span 7 columns) -->
                <div class="lg:col-span-7 flex flex-col">
                    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden h-full">
                        <div class="bg-blue-600 text-white px-5 py-3 flex justify-between items-center">
                            <h5 class="text-lg font-semibold m-0">QA Risk Assessment</h5>
                            <button type="button" @click="openGuide = true" class="bg-blue-700 bg-opacity-20 hover:bg-opacity-30 text-white text-xs font-bold py-1 px-3 rounded transition-colors">
                                View Matrix Guide
                            </button>
                        </div>
                        
                        <div class="p-5 sm:p-6">
                            @if ($is_high_risk_alert)
                                <div class="bg-red-50 border-l-4 border-red-600 text-red-800 p-4 mb-6 rounded shadow-sm font-bold">
                                    HIGH RISK: Operation must not continue or begin without mitigation to a lower risk level.
                                </div>
                            @endif

                            <form wire:submit="submitReview" class="space-y-5">
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Hazard Description (QA Refinement)</label>
                                    <textarea wire:model="qa_hazard_description" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" required></textarea>
                                    @error('qa_hazard_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Root Cause</label>
                                    <textarea wire:model="root_cause" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" required></textarea>
                                    @error('root_cause') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Immediate Action</label>
                                    <textarea wire:model="immediate_action" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" required></textarea>
                                    @error('immediate_action') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Corrective Action</label>
                                    <textarea wire:model="corrective_action" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" required></textarea>
                                    @error('corrective_action') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Risk Matrix Inputs (Reactive with wire:model.live) -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Severity (1-5)</label>
                                        <input type="number" wire:model.live="severity_rating" min="1" max="5" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-1">Likelihood (1-5)</label>
                                        <input type="number" wire:model.live="likelihood_rating" min="1" max="5" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-500 mb-1">Risk Score</label>
                                        <input type="text" value="{{ $current_risk_score }}" class="w-full bg-gray-200 border-gray-300 rounded-md text-center font-extrabold text-xl text-gray-700" disabled>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Target Compliance Date</label>
                                    <input type="date" wire:model="target_compliance_date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm" required>
                                    <p class="text-xs text-gray-500 mt-1">Auto-calculated based on Risk Level closure targets (90, 60, 30, or 15 days).</p>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-base font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-colors" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="submitReview">Complete Review</span>
                                        <span wire:loading wire:target="submitReview" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Saving Review...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    <!-- Alpine.js Offcanvas Slide-Over for Risk Matrix Guide -->
        <div x-show="openGuide" class="fixed inset-0 overflow-hidden z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="absolute inset-0 overflow-hidden">
                <!-- Background overlay -->
                <div x-show="openGuide" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="openGuide = false" aria-hidden="true"></div>

                <div class="fixed inset-y-0 right-0 max-w-full flex">
                    <div x-show="openGuide" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="w-screen max-w-4xl">
                        <div class="h-full flex flex-col bg-white shadow-xl overflow-y-scroll">
                            <div class="py-6 px-4 bg-blue-700 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-lg font-medium text-white-700" id="slide-over-title">Safety Risk Assessment Guide</h2>
                                    <div class="ml-3 h-7 flex items-center">
                                        <button type="button" @click="openGuide = false" class="bg-gray-900 rounded-md text-gray-300 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 relative flex-1 px-4 sm:px-6 pb-10">
                                
                                <!-- 1. Risk Assessment Matrix -->
                                <h6 class="font-bold text-blue-700 border-b pb-2 mb-4 text-sm">Risk Assessment Matrix (Severity x Likelihood)</h6>
                                <div class="overflow-x-auto mb-8">
                                    <table class="min-w-full text-xs text-center border-collapse border border-gray-300">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border border-gray-300 p-2"></th>
                                                <th class="border border-gray-300 p-2">Rare</th>
                                                <th class="border border-gray-300 p-2">Unlikely</th>
                                                <th class="border border-gray-300 p-2">Possible</th>
                                                <th class="border border-gray-300 p-2">Likely</th>
                                                <th class="border border-gray-300 p-2">Frequent</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-left font-bold border border-gray-300 p-2 bg-gray-50">Minor</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">1 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">2 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">3 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">4 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">5 low</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left font-bold border border-gray-300 p-2 bg-gray-50">Moderate</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">2 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">4 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffff00;">6 minor</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffff00;">8 minor</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffff00;">10 minor</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left font-bold border border-gray-300 p-2 bg-gray-50">Major</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">3 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffff00;">6 minor</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffff00;">9 minor</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffc000;">12 moderate</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffc000;">15 moderate</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left font-bold border border-gray-300 p-2 bg-gray-50">Severe</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">4 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffff00;">8 minor</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffc000;">12 moderate</td>
                                                <td class="border border-gray-300 p-2 text-white" style="background-color: #ff0000;">16 serious</td>
                                                <td class="border border-gray-300 p-2 text-white" style="background-color: #ff0000;">20 serious</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left font-bold border border-gray-300 p-2 bg-gray-50">Catastrophic</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #92d050;">5 low</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffff00;">10 minor</td>
                                                <td class="border border-gray-300 p-2 text-black" style="background-color: #ffc000;">15 moderate</td>
                                                <td class="border border-gray-300 p-2 text-white" style="background-color: #ff0000;">20 serious</td>
                                                <td class="border border-gray-300 p-2 text-white" style="background-color: #7030a0;">25 high</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- 2. Severity Rating Guide -->
                                <h6 class="font-bold text-blue-700 border-b pb-2 mb-4 text-sm mt-8">Severity Rating Guide with Examples</h6>
                                <div class="overflow-x-auto mb-8">
                                    <table class="min-w-full text-xs border-collapse border border-gray-300">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border border-gray-300 p-2 text-left w-32">Severity Level</th>
                                                <th class="border border-gray-300 p-2 text-left">Definition</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">1 Minor</td>
                                                <td class="border border-gray-300 p-3">
                                                    <p class="mb-2">No injury; negligible impact. Minor paperwork correction.</p>
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        <li><strong>Damage:</strong> Investigation found to be w/in limits</li>
                                                        <li><strong>Injury:</strong> Self-care only, medical attention not required.</li>
                                                        <li><strong>Operational:</strong> No expected impact on either airworthiness or safety.</li>
                                                        <li><strong>Compliance:</strong> Conditions exist which may result in a policy and procedure (P&P) deviation or regulatory non-compliance.</li>
                                                        <li><strong>Environmental:</strong> Potential or prevented environmental nuisance.</li>
                                                        <li><strong>Business disruption:</strong> No significant impact.</li>
                                                        <li><strong>Brand impact:</strong> No implication.</li>
                                                        <li><strong>System:</strong> Negligible process or system impact.</li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">2 Moderate</td>
                                                <td class="border border-gray-300 p-3">
                                                    <p class="mb-2">Minor injury or rework. No impact on airworthiness.</p>
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        <li><strong>Damage:</strong> Identified damage, capable of being deferred until out of service</li>
                                                        <li><strong>Injury:</strong> Medical evaluation or treatment at facility, no hospitalization required</li>
                                                        <li><strong>Operational:</strong> Gate return, delay, or cancellation. Aircraft or system reliability affected</li>
                                                        <li><strong>Compliance:</strong> Company P&P deviation or regulatory non-compliance, not security or operational</li>
                                                        <li><strong>Environmental:</strong> Harm that is below threshold of environmental nuisance and does not breach the National Environmental Policy Act (NEPA)</li>
                                                        <li><strong>Business disruption:</strong> Localized disruption of area</li>
                                                        <li><strong>Brand impact:</strong> Limited localized implication</li>
                                                        <li><strong>System:</strong> Impact to process or system identified but dependability not affected, leading to use of abnormal procedures</li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">3 Major</td>
                                                <td class="border border-gray-300 p-3">
                                                    <p class="mb-2">Injury needing medical care or major quality deviation.</p>
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        <li><strong>Damage:</strong> Requires immediate repair prior to service, possible schedule impact < 1 calendar day</li>
                                                        <li><strong>Injury:</strong> Medical treatment, hospitalization < 48 hours</li>
                                                        <li><strong>Operational:</strong> High speed Rejected Takeoff (RTO), diversion, or Operational Safety Event / Flight Test Instruction (OSE/FTI) with no emergency. Aircraft airworthiness concern impacting safety or operational capability</li>
                                                        <li><strong>Compliance:</strong> Regional or local trend of company P&P deviation or regulatory non-compliance impacting safety, security, or operational capability</li>
                                                        <li><strong>Environmental:</strong> Interferes or may interfere with the use of the area by persons occupying a place or any unsightly or offensive condition</li>
                                                        <li><strong>Business disruption:</strong> Single base shutdown ≥ 1 operational day or ≥ 3 outstations shutdown ≥ 1 operational day</li>
                                                        <li><strong>Brand impact:</strong> Regional implication</li>
                                                        <li><strong>System:</strong> Dependability of process or system affected leading to reduced safety margins; reduction in team member's ability to cope with adverse conditions</li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">4 Severe</td>
                                                <td class="border border-gray-300 p-3">
                                                    <p class="mb-2">Serious injury or regulatory finding. High impact.</p>
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        <li><strong>Damage:</strong> Requires immediate repair prior to service, possible schedule impact ≥ 1 calendar day, or National Transportation Safety Board (NTSB) defined substantial damage</li>
                                                        <li><strong>Injury:</strong> Hospitalization ≥ 48 hours within 7 days of event or NTSB serious injury</li>
                                                        <li><strong>Operational:</strong> High speed RTO, diversion, or OSE/FTI with emergency. Fleet-wide airworthiness concerns impacting safety or operational capability</li>
                                                        <li><strong>Compliance:</strong> Systemic company P&P deviation or regulatory non-compliance deviation impacting safety, security, and operational capability</li>
                                                        <li><strong>Environmental:</strong> Non-trivial harm to the health and safety of human beings, or wide scale casualties, potential loss of property</li>
                                                        <li><strong>Business disruption:</strong> Multiple base shutdown ≥ 1 operational day or ≥ 5 outstations shutdown ≥ 1 operational day</li>
                                                        <li><strong>Brand impact:</strong> National implication</li>
                                                        <li><strong>System:</strong> Partial breakdown of a process or system leading to physical distress/high workload impairing accuracy and completion of tasks</li>
                                                    </ul>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">5 Catastrophic</td>
                                                <td class="border border-gray-300 p-3">
                                                    <p class="mb-2">Fatality, aircraft/system loss, or Federal Aviation Administration (FAA) / European Union Aviation Safety Agency (EASA) violation.</p>
                                                    <ul class="list-disc pl-5 space-y-1">
                                                        <li><strong>Damage:</strong> Beyond repair, unserviceable, hull loss / early retirement</li>
                                                        <li><strong>Injury:</strong> Fatal injury</li>
                                                        <li><strong>Operational:</strong> NTSB classified accident</li>
                                                        <li><strong>Compliance:</strong> Increased regulatory oversight resulting in system limitation(s) or restriction(s)</li>
                                                        <li><strong>Environmental:</strong> Significant impact and wide scale harm to human beings and/or environment</li>
                                                        <li><strong>Business disruption:</strong> System-wide shutdown > 1 operational day</li>
                                                        <li><strong>Brand impact:</strong> Global implication</li>
                                                        <li><strong>System:</strong> Complete breakdown of process or system, operating with no meaningful safety margins</li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- 3. Likelihood Rating Guide -->
                                <h6 class="font-bold text-blue-700 border-b pb-2 mb-4 text-sm mt-8">Likelihood Rating Guide with Examples</h6>
                                <div class="overflow-x-auto mb-8">
                                    <table class="min-w-full text-xs border-collapse border border-gray-300">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border border-gray-300 p-2 text-left w-32">Severity Level</th>
                                                <th class="border border-gray-300 p-2 text-left">Definition</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">1 Rare</td>
                                                <td class="border border-gray-300 p-3">It is unlikely to occur again, or once in 2-year period.<br />Remaining controls effective in preventing/ containing negative impacts.</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">2 Unlikely</td>
                                                <td class="border border-gray-300 p-3">Possible Under exceptional circumstances, or at least once in 1-year period.<br />Remaining controls identified with reduced effectiveness in preventing/ containing negative impacts.</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">3 Possible</td>
                                                <td class="border border-gray-300 p-3">Could occur occasionally (e.g. recurring human error), or once in a 90-day period.<br />Loss 25% of controls and remaining control(s) identified with reduced effectiveness in preventing/ containing negative impacts.</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">4 Likely</td>
                                                <td class="border border-gray-300 p-3">Expected to happen periodically, at least once in 30-day period.<br />Loss 50% of controls and remaining control(s) identified with reduced effectiveness in preventing/ containing negative impacts.</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">5 Frequent</td>
                                                <td class="border border-gray-300 p-3">Occurs regularly, or at least once in 7-day period.<br />All remaining controls ineffective or no remaining controls.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- 4. Risk Acceptance and Mitigation -->
                                <h6 class="font-bold text-blue-700 border-b pb-2 mb-4 text-sm mt-8">Risk Acceptance and Mitigation</h6>
                                <div class="overflow-x-auto mb-8">
                                    <table class="min-w-full text-xs border-collapse border border-gray-300">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="border border-gray-300 p-2 text-left">Risk Level</th>
                                                <th class="border border-gray-300 p-2 text-left">Action</th>
                                                <th class="border border-gray-300 p-2 text-left">Closure date (Calendar days from QA hazard review)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">Low (1-5)</td>
                                                <td class="border border-gray-300 p-3">Mitigation plan not required unless regulatory non-compliance and/or non-conformance with company policy and procedure exists.</td>
                                                <td class="border border-gray-300 p-3">90 days</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">Minor (6-10)</td>
                                                <td class="border border-gray-300 p-3">Individual, local, or targeted mitigations recommended. Mitigation plan not required unless regulatory non-compliance and/or non-conformance with company policy and procedure exists.</td>
                                                <td class="border border-gray-300 p-3">60 days</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">Moderate (12-15)</td>
                                                <td class="border border-gray-300 p-3">Evaluate process and system to determine appropriate level (Individual, local, targeted, or systemic) of mitigations. Mitigation plan required.</td>
                                                <td class="border border-gray-300 p-3">30 days</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold">Serious (16-20)</td>
                                                <td class="border border-gray-300 p-3">Evaluate process and system to determine appropriate level of mitigation. Mitigation plan required.</td>
                                                <td class="border border-gray-300 p-3">15 days</td>
                                            </tr>
                                            <tr>
                                                <td class="border border-gray-300 p-3 font-bold text-red-600">High (25)</td>
                                                <td class="border border-gray-300 p-3 font-bold">Operation must not continue or begin without mitigation to a lower risk level.</td>
                                                <td class="border border-gray-300 p-3 font-bold">Before the operation begins or resumes</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>