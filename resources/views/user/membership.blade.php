@extends('layouts.user')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Membership Status
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

        <!-- Membership Status Card -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Membership Overview</h2>
                @php
                    $statusColor = $statusInfo['color'];
                    $statusColorClasses = [
                        'green' => 'bg-green-100 text-green-800 border-green-200',
                        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'red' => 'bg-red-100 text-red-800 border-red-200',
                        'gray' => 'bg-gray-100 text-gray-800 border-gray-200'
                    ];
                @endphp
                <span class="px-4 py-2 rounded-full text-sm font-medium border {{ $statusColorClasses[$statusColor] }}">
                    {{ $membershipData['status'] }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Status Info -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full flex items-center justify-center {{ $statusColor === 'green' ? 'bg-green-100' : ($statusColor === 'yellow' ? 'bg-yellow-100' : ($statusColor === 'red' ? 'bg-red-100' : 'bg-gray-100')) }}">
                        @if($statusInfo['icon'] === 'check-circle')
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($statusInfo['icon'] === 'clock')
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @elseif($statusInfo['icon'] === 'x-circle')
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @else
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>
                    <h3 class="font-semibold text-gray-800">Status</h3>
                    <p class="text-sm text-gray-600">{{ $membershipData['status'] }}</p>
                </div>

                <!-- Membership Type -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Type</h3>
                    <p class="text-sm text-gray-600">{{ $membershipData['type'] }}</p>
                </div>

                <!-- Payment Amount -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-purple-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Payment</h3>
                    <p class="text-sm text-gray-600">₱{{ number_format($membershipData['payment_amount']) }}</p>
                </div>

                <!-- Member Since -->
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-indigo-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">Member Since</h3>
                    <p class="text-sm text-gray-600">
                        @if($membershipData['created_at'])
                            {{ $membershipData['created_at']->format('M Y') }}
                        @else
                            Not Available
                        @endif
                    </p>
                </div>
            </div>

            <!-- Status Message -->
            <div class="mt-6 p-4 rounded-lg {{ $statusColor === 'green' ? 'bg-green-50 border border-green-200' : ($statusColor === 'yellow' ? 'bg-yellow-50 border border-yellow-200' : ($statusColor === 'red' ? 'bg-red-50 border border-red-200' : 'bg-gray-50 border border-gray-200')) }}">
                <p class="text-sm {{ $statusColor === 'green' ? 'text-green-800' : ($statusColor === 'yellow' ? 'text-yellow-800' : ($statusColor === 'red' ? 'text-red-800' : 'text-gray-800')) }}">
                    {{ $statusInfo['message'] }}
                </p>
                @if($statusInfo['action'])
                    <div class="mt-3">
                        @if($membershipData['status'] === 'Inactive' || $membershipData['status'] === 'Not Set')
                            <a href="https://cashier.bestlink-sms.com/login" target="_blank" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                                {{ $statusInfo['action'] }}
                            </a>
                        @else
                            <span class="text-sm font-medium {{ $statusColor === 'yellow' ? 'text-yellow-700' : 'text-gray-700' }}">{{ $statusInfo['action'] }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>



        <!-- Call to Action -->
        <div class="mt-8 bg-blue-600 rounded-lg shadow-lg p-8 text-white text-center">
            <h3 class="text-2xl font-bold mb-4">Join Our Alumni Community Today!</h3>
            <p class="text-lg mb-6">Stay connected with your fellow alumni.</p>
            <div class="space-x-4">
                <a href="https://www.facebook.com/BestlinkAlumni" target="_blank" class="inline-block border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-colors duration-200">
                    Join us!
                </a>
            </div>
        </div>

        <!-- Personal Information -->
        @if($membershipData['has_record'])
            <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Full Name</label>
                        <p class="text-gray-800">{{ $membershipData['fullname'] }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Student Number</label>
                        <p class="text-gray-800">{{ $membershipData['student_number'] }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Course</label>
                        <p class="text-gray-800">{{ $membershipData['course'] }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Batch</label>
                        <p class="text-gray-800">{{ $membershipData['batch'] }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection