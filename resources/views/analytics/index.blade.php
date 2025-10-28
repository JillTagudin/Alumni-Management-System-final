<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- KPI Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $engagement_metrics['total_users'] }}</p>
                    <p class="text-sm text-green-600">↗ 12% from last month</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Active Users (30d)</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $engagement_metrics['active_users_30d'] }}</p>
                    <p class="text-sm text-gray-600">{{ $engagement_metrics['engagement_rate'] }}% engagement rate</p>
                </div>
                
                <!-- Add more KPI cards -->
            </div>
            
            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- User Growth Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">User Growth Over Time</h3>
                    <canvas id="userGrowthChart" height="300"></canvas>
                </div>
                
                <!-- Activity Heatmap -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Activity Heatmap</h3>
                    <div id="activityHeatmap"></div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>