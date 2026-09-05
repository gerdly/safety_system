<div x-data="{ 
        activeTab: 'risk',
        hasData: {{ $initialChartData['hasData'] ? 'true' : 'false' }},
        isFullYear: {{ $initialChartData['isFullYear'] ? 'true' : 'false' }}
    }" 
    @toggle-empty-state.window="hasData = $event.detail.hasData; isFullYear = $event.detail.isFullYear"
    class="w-full">
    
    <!-- Dashboard Header & Filters -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h2 class="text-3xl font-extrabold text-gray-900">SMS Program Summary</h2>
        <div class="flex space-x-3 mt-4 sm:mt-0">
            <!-- Livewire dynamically tracks filter changes -->
            <select wire:model.live="selectedYear" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @foreach($availableYears as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
            <select wire:model.live="selectedPeriod" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="Full Year">Full Year</option>
                <option value="Q1">Q1</option>
                <option value="Q2">Q2</option>
                <option value="Q3">Q3</option>
                <option value="Q4">Q4</option>
            </select>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <!-- Switch to Risk Tab -->
            <button @click.prevent="activeTab = 'risk'" 
                    :class="{ 'border-blue-500 text-blue-600 font-bold': activeTab === 'risk', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium': activeTab !== 'risk' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-colors outline-none focus:outline-none">
                Risk & Company Analysis
            </button>
            
            <!-- Switch to Monthly Tab and trigger resize for ApexCharts to render properly when unhidden -->
            <button @click.prevent="activeTab = 'monthly'; setTimeout(() => window.dispatchEvent(new Event('resize')), 50);" 
                    :class="{ 'border-blue-500 text-blue-600 font-bold': activeTab === 'monthly', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium': activeTab !== 'monthly' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-colors outline-none focus:outline-none">
                Monthly Performance
            </button>
        </nav>
    </div>

  <!-- Tab 1: Risk & Company Analysis -->
    <div x-show="hasData && activeTab === 'risk'" x-cloak class="grid grid-cols-1 gap-6" :class="isFullYear ? 'lg:grid-cols-3' : 'lg:grid-cols-1'">
        
        <!-- Risk Trend Chart: Expands to full width if isFullYear is false -->
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6" :class="isFullYear ? 'lg:col-span-2' : 'col-span-1'">
            <h3 class="text-lg text-gray-700 mb-4">Average of Severity, Likelihood and Risk Score by Date</h3>
            <div id="riskTrendChart" wire:ignore></div>
        </div>
        
        <!-- Quarter Pie Chart: Only shows when viewing the Full Year -->
        <div x-show="isFullYear" class="lg:col-span-1 bg-white shadow-sm border border-gray-200 rounded-lg p-6 flex flex-col justify-center items-center">
            <h3 class="text-lg text-gray-700 mb-4">Hazard Reports by Quarter</h3>
            <div id="quarterPieChart" class="w-full" wire:ignore></div>
        </div>
    </div>

    <!-- Tab 2: Monthly Performance -->
   <div x-show="hasData && activeTab === 'monthly'" x-cloak style="display: none;" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg text-gray-700 mb-4">Sum and Average of Response Time by Month</h3>
            <div id="responseTimeChart" wire:ignore></div>
        </div>
        
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg text-gray-700 mb-4">Hazard Reports by Month</h3>
            <div id="monthlyReportsChart" wire:ignore></div>
        </div>
    </div>
    <!-- No Data Empty State -->
    <div x-show="!hasData" x-cloak class="bg-gray-50 border border-gray-200 rounded-lg p-12 text-center my-8">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-sm font-semibold text-gray-900">No reports found</h3>
        <p class="mt-1 text-sm text-gray-500">There are no hazard reports recorded for the selected period.</p>
    </div>

    <!-- ApexCharts Initialization -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let initialData = @json($initialChartData);

            // 1. Risk Trend Line Chart
            let riskOptions = {
                chart: { type: 'line', height: 350, toolbar: { show: true } },
                stroke: { width: 4, curve: 'straight' },
                series: [
                    { name: 'Avg Severity', data: initialData.riskTrend.severity },
                    { name: 'Avg Likelihood', data: initialData.riskTrend.likelihood },
                    { name: 'Avg Risk Score', data: initialData.riskTrend.risk }
                ],
                xaxis: { categories: initialData.riskTrend.categories },
                colors: ['#1e3a8a', '#64748b', '#ea580c'], 
                legend: { position: 'bottom' }
            };
            let riskChart = new ApexCharts(document.querySelector("#riskTrendChart"), riskOptions);
            riskChart.render();

            // 2. Quarter Pie Chart
            let pieOptions = {
                chart: { type: 'pie', height: 320 },
                series: initialData.quarter.series.length > 0 ? initialData.quarter.series : [1],
                labels: initialData.quarter.labels.length > 0 ? initialData.quarter.labels : ['No Data'],
                // Colors representing the 4 quarters
                colors: ['#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6'],
                legend: { position: 'bottom' },
                dataLabels: { 
                    enabled: true,
                    formatter: function (val) {
                        return Math.round(val) + "%"
                    }
                }
            };
            let quarterChart = new ApexCharts(document.querySelector("#quarterPieChart"), pieOptions);
            quarterChart.render();

            // 3. Response Time Line Chart
            let responseOptions = {
                chart: { type: 'line', height: 350, toolbar: { show: true } },
                stroke: { width: 4, curve: 'straight' },
                series: [
                    { name: 'Sum of Response Time', data: initialData.monthly.sumResponse },
                    { name: 'Average Response Time', data: initialData.monthly.avgResponse }
                ],
                xaxis: { categories: initialData.monthly.categories },
                colors: ['#3b82f6', '#10b981'],
                legend: { position: 'bottom' }
            };
            let responseChart = new ApexCharts(document.querySelector("#responseTimeChart"), responseOptions);
            responseChart.render();

            // 4. Monthly Bar Chart
            let barOptions = {
                chart: { type: 'bar', height: 350, toolbar: { show: true } },
                series: [{ name: 'Hazard Reports', data: initialData.monthly.totalReports }],
                xaxis: { categories: initialData.monthly.categories },
                colors: ['#3ba2f6'],
                plotOptions: { bar: { columnWidth: '60%' } }
            };
            let barChart = new ApexCharts(document.querySelector("#monthlyReportsChart"), barOptions);
            barChart.render();

           // Listen for Livewire updates dispatched from the backend controller
            Livewire.on('update-charts', (event) => {
                let newData = event.chartData || (event[0] && event[0].chartData); 
                if (!newData) return;

                // Safely update Alpine component state passing both flags
                window.dispatchEvent(new CustomEvent('toggle-empty-state', { 
                    detail: { hasData: newData.hasData, isFullYear: newData.isFullYear } 
                }));

                if (!newData.hasData) return;
                
                // CRITICAL FIX: Wait 50ms for Alpine to remove display:none from the containers 
                // before asking ApexCharts to calculate SVG dimensions and redraw.
                setTimeout(() => {
                    // 1. Update risk trend chart
                    riskChart.updateSeries([
                        { name: 'Avg Severity',data: newData.riskTrend.severity },
                        { name: 'Avg Likelihood',data: newData.riskTrend.likelihood },
                        { name: 'Avg Risk Score',data: newData.riskTrend.risk }
                    ]);
                    riskChart.updateOptions({ xaxis: { categories: newData.riskTrend.categories } });

                    // 2. Update quarter pie chart (Only if full year is active)
                    if (newData.isFullYear) {
                        let qSeries = newData.quarter.series.length > 0 ? newData.quarter.series : [0];
                        let qLabels = newData.quarter.labels.length > 0 ? newData.quarter.labels : ['No Data'];
                        
                        quarterChart.updateSeries(qSeries);
                        quarterChart.updateOptions({ labels: qLabels });
                    }

                    // 3. Update response time chart
                    responseChart.updateSeries([
                        { name: 'Sum Response Time', data: newData.monthly.sumResponse },
                        { name: 'Avg Response Time', data: newData.monthly.avgResponse }
                    ]);
                    responseChart.updateOptions({ xaxis: { categories: newData.monthly.categories } });

                    // 4. Update monthly reports bar chart
                    barChart.updateSeries([{ data: newData.monthly.totalReports }]);
                    barChart.updateOptions({ xaxis: { categories: newData.monthly.categories } });
                    
                    // Trigger a manual window resize event so the line chart recalculates its new full-width
                    window.dispatchEvent(new Event('resize'));
                }, 50);
            });
        });
    </script>
</div>