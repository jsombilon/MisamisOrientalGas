<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-lg font-medium mb-4">Orders Chart</h2>

                    <!-- Chart Container -->
                    <div class="w-full h-72">
                        <canvas id="ordersChart" class="w-full h-full"></canvas>
                    </div>

                    <!-- Filter Checkboxes -->
                    <div class="flex flex-wrap gap-4 mt-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="monthFilter" checked class="h-4 w-4 text-blue-600 rounded">
                            <span>Month</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="weekFilter" class="h-4 w-4 text-blue-600 rounded">
                            <span>Week</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="dayFilter" class="h-4 w-4 text-blue-600 rounded">
                            <span>Day</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
   

    <script>
        const ctx = document.getElementById('ordersChart').getContext('2d');

        // Dummy datasets
        const dataSets = {
            month: {
                labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                data: [120, 150, 200, 180, 90, 75, 130, 160, 210, 190, 170, 220]
            },
            week: {
                labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
                data: [45, 60, 30, 50]
            },
            day: {
                labels: ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"],
                data: [12, 18, 9, 14, 20, 25, 11]
            }
        };

        // Create chart
        const ordersChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: dataSets.month.labels,
                datasets: [{
                    label: 'Orders',
                    data: dataSets.month.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Function to update chart based on selected filter
        function updateChart() {
            if (document.getElementById('monthFilter').checked) {
                ordersChart.data.labels = dataSets.month.labels;
                ordersChart.data.datasets[0].data = dataSets.month.data;
            } else if (document.getElementById('weekFilter').checked) {
                ordersChart.data.labels = dataSets.week.labels;
                ordersChart.data.datasets[0].data = dataSets.week.data;
            } else if (document.getElementById('dayFilter').checked) {
                ordersChart.data.labels = dataSets.day.labels;
                ordersChart.data.datasets[0].data = dataSets.day.data;
            }
            ordersChart.update();
        }

        // Attach event listeners
        document.getElementById('monthFilter').addEventListener('change', function() {
            this.checked = true;
            document.getElementById('weekFilter').checked = false;
            document.getElementById('dayFilter').checked = false;
            updateChart();
        });

        document.getElementById('weekFilter').addEventListener('change', function() {
            this.checked = true;
            document.getElementById('monthFilter').checked = false;
            document.getElementById('dayFilter').checked = false;
            updateChart();
        });

        document.getElementById('dayFilter').addEventListener('change', function() {
            this.checked = true;
            document.getElementById('monthFilter').checked = false;
            document.getElementById('weekFilter').checked = false;
            updateChart();
        });
    </script>

</x-app-layout>
