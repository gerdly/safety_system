<div> 
    
    <div class="print-container">
        <!-- Action buttons hidden during print -->
        <div class="no-print mb-4 flex justify-between bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
            <a href="{{ route('hazards.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                &larr; Back to Dashboard
            </a>
            <button onclick="window.print()" class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-bold rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none shadow-sm transition-colors">
                Print / Save as PDF
            </button>
        </div>

        <!-- Printable Document Area -->
        <div class="document-page">

            <!-- Master layout table to force native printing headers and footers -->
            <table class="print-layout-table">

                <!-- Native Print Header -->
                <thead>
                    <tr>
                        <td>
                            <div class="header-section flex flex-col items-center">
                                <div class="logo-placeholder mb-2">
                                    <!-- Ensure the logo exists in public/img/ or change the path -->
                                    <img src="{{ asset('img/sms-logo.png') }}" alt="NexGen Logo" style="max-height: 50px; object-fit: contain;" />
                                </div>
                                <h3 class="document-title">Hazard Report Safety Risk Assessment Form</h3>
                            </div>
                        </td>
                    </tr>
                </thead>

                <!-- Native Print Body -->
                <tbody>
                    <tr>
                        <td>
                            <!-- Main Data Table -->
                            <table class="report-table">
                                <tbody>
                                    <!-- Employee Report Section -->
                                    <tr class="section-header">
                                        <td colspan="2">Employee report</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Name</td>
                                        <td>{{ empty($report->reporter_name) ? 'Anonymous' : $report->reporter_name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Date</td>
                                        <td>{{ \Carbon\Carbon::parse($report->incident_date)->format('Y-m-d') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Department/Area</td>
                                        <td>{{ $report->department_area ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Hazard description</td>
                                        <td>{{ $report->employee_hazard_description }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Suggested Mitigation</td>
                                        <td>{{ $report->suggested_mitigation ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label-col">Company</td>
                                        <td>{{ $report->company->name ?? 'N/A' }}</td>
                                    </tr>

                                    <!-- QA Hazard Review Section -->
                                    <tr class="section-header mt-4">
                                        <td colspan="2">QA Hazard Review</td>
                                    </tr>
                                    @if ($report->qaReview)
                                        <tr>
                                            <td class="label-col">Corrective Action</td>
                                            <td>{{ $report->qaReview->corrective_action }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Hazard Description</td>
                                            <td>{{ $report->qaReview->qa_hazard_description }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Root cause (if known)</td>
                                            <td>{{ $report->qaReview->root_cause }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Immediate action (if any)</td>
                                            <td>{{ $report->qaReview->immediate_action }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Severity rating</td>
                                            <td>{{ $report->qaReview->severity_rating }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Likelihood rating</td>
                                            <td>{{ $report->qaReview->likelihood_rating }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Risk Score</td>
                                            <td>{{ $report->qaReview->risk_score }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Compliance time</td>
                                            <td>{{ \Carbon\Carbon::parse($report->qaReview->target_compliance_date)->format('n/j/Y g:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Closure Date</td>
                                            <td>
                                                {{ $report->qaReview->actual_closure_date ? \Carbon\Carbon::parse($report->qaReview->actual_closure_date)->format('n/j/Y g:i A') : 'Pending' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="label-col">Date</td>
                                            <td>{{ \Carbon\Carbon::parse($report->qaReview->qa_review_date)->format('n/j/Y g:i A') }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center text-gray-500 italic py-5">
                                                Review pending
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <h5 class="matrix-title">Risk Assessment Matrix (Severity Rating X Likelihood)</h5>

                            <!-- Recreated Static Risk Matrix -->
                            <table class="matrix-table">
                                <thead>
                                    <tr>
                                        <th style="background-color: white; border-top: none; border-left: none;"></th>
                                        <th>Rare</th>
                                        <th>Unlikely</th>
                                        <th>Possible</th>
                                        <th>Likely</th>
                                        <th>Frequent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="row-header font-bold">Minor</td>
                                        <td class="bg-low">1 low</td>
                                        <td class="bg-low">2 low</td>
                                        <td class="bg-low">3 low</td>
                                        <td class="bg-low">4 low</td>
                                        <td class="bg-low">5 low</td>
                                    </tr>
                                    <tr>
                                        <td class="row-header font-bold">Moderate</td>
                                        <td class="bg-low">2 low</td>
                                        <td class="bg-low">4 low</td>
                                        <td class="bg-minor">6 minor</td>
                                        <td class="bg-minor">8 minor</td>
                                        <td class="bg-minor">10 minor</td>
                                    </tr>
                                    <tr>
                                        <td class="row-header font-bold">Major</td>
                                        <td class="bg-low">3 low</td>
                                        <td class="bg-minor">6 minor</td>
                                        <td class="bg-minor">9 minor</td>
                                        <td class="bg-moderate">12 moderate</td>
                                        <td class="bg-moderate">15 moderate</td>
                                    </tr>
                                    <tr>
                                        <td class="row-header font-bold">Severe</td>
                                        <td class="bg-low">4 low</td>
                                        <td class="bg-minor">8 minor</td>
                                        <td class="bg-moderate">12 moderate</td>
                                        <td class="bg-serious">16 serious</td>
                                        <td class="bg-serious">20 serious</td>
                                    </tr>
                                    <tr>
                                        <td class="row-header font-bold">Catastrophic</td>
                                        <td class="bg-low">5 low</td>
                                        <td class="bg-minor">10 minor</td>
                                        <td class="bg-moderate">15 moderate</td>
                                        <td class="bg-serious">20 serious</td>
                                        <td class="bg-high">25 high</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>

                <!-- Native Print Footer -->
                <tfoot>
                    <tr>
                        <td>
                            <div class="document-footer">
                                <span>SMS-2</span>
                                <span>REV: 1</span>
                                <span>DATE: 12/15/2025</span>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Scoped CSS movido hacia adentro del DIV principal -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        .print-container {
            max-width: 850px;
            margin: 0 auto;
            padding: 20px;
        }

        .document-page {
            background-color: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: Arial, sans-serif;
            color: #000;
        }

        /* Master print layout table styling to remain invisible */
        .print-layout-table {
            width: 100%;
            border: none;
        }

        .print-layout-table > thead > tr > td,
        .print-layout-table > tbody > tr > td,
        .print-layout-table > tfoot > tr > td {
            border: none;
            padding: 0;
        }

        .header-section {
            margin-bottom: 20px;
        }

        .document-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        /* Target only the inner data tables to apply borders */
        .report-table, .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .report-table th, .report-table td,
        .matrix-table th, .matrix-table td {
            border: 1px solid black;
            padding: 6px 10px;
            vertical-align: top;
        }

        .report-table .section-header {
            background-color: #9bc2e6; /* Light blue matching the Word doc */
            font-weight: bold;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .report-table .label-col {
            width: 35%;
            font-weight: bold;
        }

        .matrix-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            margin-top: 25px;
        }

        .matrix-table {
            width: auto;
        }

        .matrix-table th {
            font-weight: bold;
            text-align: center;
            background-color: #f3f4f6;
        }

        .matrix-table .row-header {
            background-color: transparent;
            text-align: left;
        }

        /* Strict background colors for the matrix ensuring they print */
        .bg-low {
            background-color: #92d050 !important;
            color: black !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-minor {
            background-color: #ffff00 !important;
            color: black !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-moderate {
            background-color: #ffc000 !important;
            color: black !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-serious {
            background-color: #ff0000 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .bg-high {
            background-color: #7030a0 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .document-footer {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #666;
            padding-top: 15px;
            margin-top: 20px;
            border-top: 1px solid #ddd;
        }

        /* Print specific CSS */
        @media print {
            body {
                background-color: white;
            }

            .no-print {
                display: none !important;
            }

            .print-container {
                padding: 0;
                max-width: 100%;
                margin: 0;
            }

            .document-page {
                box-shadow: none;
                padding: 0;
            }

            /* Ensure printer uses standard margins */
            @page {
                size: letter;
                margin: 0.5in;
            }
        }
    </style>

</div>