<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HazardReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SmsAnalyticsChart extends Component
{
    public $selectedYear;
    public $selectedPeriod = 'Full Year';
    public array $availableYears = [];

    public function mount()
    {
        // Fetch distinct years from existing records in the database
        $this->availableYears = HazardReport::selectRaw('YEAR(incident_date) as year')
            ->whereNotNull('incident_date')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Fallback to current year if the database is completely empty
        if (empty($this->availableYears)) {
            $this->availableYears = [date('Y')];
        }
        
        // Default to the most recent year in the database
        $this->selectedYear = $this->availableYears[0];
    }

    // Hook triggered automatically when selectedYear changes in the dropdown
    public function updatedSelectedYear()
    {
        $this->dispatchChartsUpdate();
    }

    // Hook triggered automatically when selectedPeriod changes
    public function updatedSelectedPeriod()
    {
        $this->dispatchChartsUpdate();
    }

    private function dispatchChartsUpdate()
    {
        // Dispatch browser event with new data to update ApexCharts seamlessly
        $this->dispatch('update-charts', chartData: $this->getAggregatedData());
    }

    private function getAggregatedData()
    {
        $monthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // 1. Establish strict, localized filter variables
        $startMonth = null;
        $endMonth = null;
        
        if ($this->selectedPeriod !== 'Full Year') {
            $quarterMonths = [
                'Q1' => [1, 3],
                'Q2' => [4, 6],
                'Q3' => [7, 9],
                'Q4' => [10, 12],
            ];
            
            if (array_key_exists($this->selectedPeriod, $quarterMonths)) {
                $startMonth = $quarterMonths[$this->selectedPeriod][0];
                $endMonth = $quarterMonths[$this->selectedPeriod][1];
            }
        }

        // 2. Risk Trend Data 
        // Use when() to safely inject month filters only if a quarter is selected
        $riskTrend = HazardReport::query()
            ->join('qa_reviews', 'hazard_reports.id', '=', 'qa_reviews.hazard_report_id')
            ->whereYear('hazard_reports.incident_date', $this->selectedYear)
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereMonth('hazard_reports.incident_date', '>=', $startMonth)
                             ->whereMonth('hazard_reports.incident_date', '<=', $endMonth);
            })
            ->select(
                DB::raw('DATE(hazard_reports.incident_date) as date'),
                DB::raw('ROUND(AVG(qa_reviews.severity_rating), 2) as avg_severity'),
                DB::raw('ROUND(AVG(qa_reviews.likelihood_rating), 2) as avg_likelihood'),
                DB::raw('ROUND(AVG(qa_reviews.severity_rating * qa_reviews.likelihood_rating), 2) as avg_risk')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

      // 3. Hazard Reports by Quarter 
        // Note: This specific query intentionally ignores $startMonth and $endMonth filters 
        // to always show the full year distribution context.
        $quarterData = HazardReport::query()
            ->whereYear('incident_date', $this->selectedYear)
            ->select(DB::raw('QUARTER(incident_date) as quarter'), DB::raw('COUNT(*) as total'))
            ->groupBy('quarter')
            ->orderBy('quarter')
            ->get();

        // Format quarter data arrays
        $quarterLabels = [];
        $quarterSeries = [];
        foreach ($quarterData as $data) {
            $quarterLabels[] = 'Q' . $data->quarter;
            $quarterSeries[] = $data->total;
        }

        // 4. Monthly Total Reports 
        $monthlyTotals = HazardReport::query()
            ->whereYear('incident_date', $this->selectedYear)
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereMonth('incident_date', '>=', $startMonth)
                             ->whereMonth('incident_date', '<=', $endMonth);
            })
            ->select(DB::raw('MONTH(incident_date) as month'), DB::raw('COUNT(*) as total'))
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // 5. Monthly Response Time 
        $monthlyResponse = HazardReport::query()
            ->join('qa_reviews', 'hazard_reports.id', '=', 'qa_reviews.hazard_report_id')
            ->whereYear('hazard_reports.incident_date', $this->selectedYear)
            ->whereNotNull('qa_reviews.actual_closure_date')
            ->when($startMonth && $endMonth, function ($query) use ($startMonth, $endMonth) {
                return $query->whereMonth('hazard_reports.incident_date', '>=', $startMonth)
                             ->whereMonth('hazard_reports.incident_date', '<=', $endMonth);
            })
            ->select(
                DB::raw('MONTH(hazard_reports.incident_date) as month'),
                DB::raw('SUM(DATEDIFF(qa_reviews.actual_closure_date, hazard_reports.incident_date)) as sum_response'),
                DB::raw('ROUND(AVG(DATEDIFF(qa_reviews.actual_closure_date, hazard_reports.incident_date)), 2) as avg_response')
            )
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        // Align X-axis categories dynamically
        $allMonths = collect($monthlyTotals->keys())
            ->merge($monthlyResponse->keys())
            ->unique()
            ->sort()
            ->values();

        $monthlyCategories = [];
        $sumResponse = [];
        $avgResponse = [];
        $totalReports = [];

        foreach ($allMonths as $m) {
            $monthlyCategories[] = $monthsLabels[$m - 1];
            $sumResponse[] = $monthlyResponse->has($m) ? $monthlyResponse[$m]->sum_response : 0;
            $avgResponse[] = $monthlyResponse->has($m) ? $monthlyResponse[$m]->avg_response : 0;
            $totalReports[] = $monthlyTotals->has($m) ? $monthlyTotals[$m]->total : 0;
        }

        // Return clean structure. If a dataset is empty, it returns empty arrays.
       return [
            // Flag to tell the frontend if the entire dataset is empty for the selected filters
            'hasData' => $riskTrend->isNotEmpty() || $quarterData->isNotEmpty() || $monthlyTotals->isNotEmpty(),
           
            // Flag to tell the frontend if we are viewing the full year
            'isFullYear' => $this->selectedPeriod === 'Full Year',

            'riskTrend' => [
               'categories' => array_values($riskTrend->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray()),
                'severity' => array_values($riskTrend->pluck('avg_severity')->toArray()),
                'likelihood' => array_values($riskTrend->pluck('avg_likelihood')->toArray()),
                'risk' => array_values($riskTrend->pluck('avg_risk')->toArray()),
            ],
            // New Quarter data replacing Company/Department
            'quarter' => [
                'labels' => $quarterLabels,
                'series' => $quarterSeries,
            ],
            'monthly' => [
                'categories' => $monthlyCategories,
                'sumResponse' => $sumResponse,
                'avgResponse' => $avgResponse,
                'totalReports' => $totalReports,
            ]
        ];
    }


    public function render()
    {
        return view('livewire.sms-analytics-chart', [
            'initialChartData' => $this->getAggregatedData()
        ]);
    }
}