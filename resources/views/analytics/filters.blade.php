<div class="bg-white p-6 rounded-lg shadow mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Date Range</label>
            <select name="date_range" class="mt-1 block w-full rounded-md border-gray-300">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 3 months</option>
                <option value="365">Last year</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700">User Role</label>
            <select name="role" class="mt-1 block w-full rounded-md border-gray-300">
                <option value="">All Roles</option>
                <option value="Admin">Admin</option>
                <option value="Staff">Staff</option>
                <option value="Alumni">Alumni</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Activity Type</label>
            <select name="activity" class="mt-1 block w-full rounded-md border-gray-300">
                <option value="">All Activities</option>
                <option value="login">Logins</option>
                <option value="alumni_create">Alumni Creation</option>
                <option value="alumni_update">Alumni Updates</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                Apply Filters
            </button>
        </div>
    </form>
</div>