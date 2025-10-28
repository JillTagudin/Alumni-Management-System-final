<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alumni Analytics Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Alumni Records & Membership Analytics - Percentage Reports</h3>
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <p class="text-blue-800 font-semibold">Total Alumni Records: {{ number_format($percentageReports['total_alumni']) }}</p>
                    </div>

                    <!-- Export Options -->
                    @if(!empty($percentageReports['gender']) || !empty($percentageReports['age_groups']) || !empty($percentageReports['membership_status']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Export Options</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- PDF Export -->
                                <form action="{{ route('reports.export') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="type" value="summary">
                                    <input type="hidden" name="format" value="pdf">
                                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path>
                                        </svg>
                                        Export PDF Report
                                    </button>
                                </form>

                                <!-- Excel Export -->
                                <form action="{{ route('reports.export') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="type" value="detailed">
                                    <input type="hidden" name="format" value="excel">
                                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1 1v-3zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Export Excel Report
                                    </button>
                                </form>

                                <!-- Summary Report -->
                                <form action="{{ route('reports.export') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="type" value="summary">
                                    <input type="hidden" name="format" value="csv">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Export CSV Summary
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Gender Distribution -->
                    @if(!empty($percentageReports['gender']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Gender Distribution</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($percentageReports['gender'] as $item)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                                        <span class="text-lg font-bold text-blue-600">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item['count']) }} alumni</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Age Groups Distribution -->
                    @if(!empty($percentageReports['age_groups']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Age Groups Distribution</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($percentageReports['age_groups'] as $item)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-700">{{ $item['label'] }} years</span>
                                        <span class="text-lg font-bold text-green-600">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item['count']) }} alumni</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Membership Status Distribution -->
                    @if(!empty($percentageReports['membership_status']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Membership Status Distribution</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($percentageReports['membership_status'] as $item)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                                        <span class="text-lg font-bold text-purple-600">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item['count']) }} alumni</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Employment Status Distribution -->
                    @if(!empty($percentageReports['employment_status']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Top Employment/Occupation Distribution</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($percentageReports['employment_status'] as $item)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                                        <span class="text-lg font-bold text-orange-600">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item['count']) }} alumni</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Course Distribution -->
                    @if(!empty($percentageReports['courses']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Course Distribution</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($percentageReports['courses'] as $item)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                                        <span class="text-lg font-bold text-indigo-600">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item['count']) }} alumni</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Location Distribution -->
                    @if(!empty($percentageReports['locations']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Top Location Distribution</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($percentageReports['locations'] as $item)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-700">{{ $item['label'] }}</span>
                                        <span class="text-lg font-bold text-red-600">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-red-600 h-2 rounded-full" style="width: {{ $item['percentage'] }}%"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item['count']) }} alumni</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Batch Distribution -->
                    @if(!empty($percentageReports['batches']))
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-3">Batch Distribution</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($percentageReports['batches'] as $item)
                                <div class="bg-white p-4 rounded-lg border">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium text-gray-700">Batch {{ $item['label'] }}</span>
                                        <!-- Lime 600 (#65a30d) text -->
                                        <span class="text-lg font-bold" style="color: #65a30d;">{{ $item['percentage'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <!-- Lime 500 (#84cc16) bar -->
                                        <div class="h-2 rounded-full" style="width: {{ (float) $item['percentage'] }}%; background-color: #84cc16;"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ number_format($item['count']) }} alumni</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(empty($percentageReports['gender']) && empty($percentageReports['age_groups']) && empty($percentageReports['membership_status']))
                    <div class="text-center py-8">
                        <div class="text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1 1v-3zM0 8a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-lg font-semibold">No Alumni Data Available</p>
                            <p class="text-sm mt-2">Add alumni records to generate percentage reports</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

