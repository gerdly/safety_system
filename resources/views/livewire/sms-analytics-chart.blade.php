<div x-data="{ activeTab: 'risk' }" class="w-full">
    
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
                    :class="activeTab === 'risk' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors">
                Risk & Company Analysis
            </button>
            <!-- Switch to Monthly Tab and trigger resize for ApexCharts to render properly when unhidden -->
            <button @click.prevent="activeTab = 'monthly'; setTimeout(() => window.dispatchEvent(new Event('resize')), 50);" 
                    :class="activeTab === 'monthly' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                Monthly Performance
            </button>
        </nav>
    </div>

    <!-- Tab 1: Risk & Company Analysis -->
    <div x-show="activeTab === 'risk'" x-cloak class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white shadow-sm border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg text-gray-700 mb-4">Average of Severity, Likelihood and Risk Score by Date</h3>
            <div id="riskTrendChart" wire:ignore></div>
        </div>
        
        <div class="lg:col-span-1 bg-white shadow-sm border border-gray-200 rounded-lg p-6 flex flex-col justify-center items-center">
            <h3 class="text-lg text-gray-700 mb-4 w-full text-left">Hazard Reports by Company</h3>
            <div id="companyPieChart" class="w-full" wire:ignore></div>
        </div>
    </div>

    <!-- Tab 2: Monthly Performance -->
    <div x-show="activeTab === 'monthly'" x-cloak style="display: none;" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg text-gray-700 mb-4">Sum and Average of Response Time by Month</h3>
            <div id="responseTimeChart" wire:ignore></div>
        </div>
        
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg text-gray-700 mb-4">Hazard Reports by Month</h3>
            <div id="monthlyReportsChart" wire:ignore></div>
        </div>
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
                colors: ['#3b82f6', '#10b981', '#d97706'], 
                legend: { position: 'bottom' }
            };
            let riskChart = new ApexCharts(document.querySelector("#riskTrendChart"), riskOptions);
            riskChart.render();

            // 2. Company Pie Chart
            let pieOptions = {
                chart: { type: 'pie', height: 300 },
                series: initialData.company.series.length > 0 ? initialData.company.series : [1],
                labels: initialData.company.labels.length > 0 ? initialData.company.labels : ['No Data'],
                colors: ['#0ea5e9', '#10b981', '#d97706'],
                legend: { position: 'right' },
                dataLabels: { enabled: false }
            };
            let pieChart = new ApexCharts(document.querySelector("#companyPieChart"), pieOptions);
            pieChart.render();

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
                // Safe compatibility for Livewire v3 payload structures
                let newData = event.chartData || (event[0] && event[0].chartData); 
                
                if (!newData) {
                    console.error("No data received from server", event);
                    return;
                }
                
                // 1. Update risk trend chart
                riskChart.updateSeries([
                    { data: newData.riskTrend.severity },
                    { data: newData.riskTrend.likelihood },
                    { data: newData.riskTrend.risk }
                ]);
                riskChart.updateOptions({ xaxis: { categories: newData.riskTrend.categories } });

                // 2. Update company pie chart
                pieChart.updateSeries(newData.company.series.length > 0 ? newData.company.series : [1]);
                pieChart.updateOptions({ labels: newData.company.labels.length > 0 ? newData.company.labels : ['No Data'] });

                // 3. Update response time chart
                responseChart.updateSeries([
                    { data: newData.monthly.sumResponse },
                    { data: newData.monthly.avgResponse }
                ]);
                responseChart.updateOptions({ xaxis: { categories: newData.monthly.categories } });

                // 4. Update monthly reports bar chart
                barChart.updateSeries([{ data: newData.monthly.totalReports }]);
                barChart.updateOptions({ xaxis: { categories: newData.monthly.categories } });
            });
        });
    </script>
</div>