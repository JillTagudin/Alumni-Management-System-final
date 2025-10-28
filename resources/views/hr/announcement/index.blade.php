@extends('layouts.admin')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('View Announcements') }}
    </h2>
@endsection

@section('content')
    <div class="py-8 min-h-screen" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('Images/bantay.jpg') }}'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($announcements->count() > 0)
                <!-- YouTube-style Single Column Layout -->
                <div class="max-w-4xl mx-auto space-y-6">
                    @foreach($announcements as $announcement)
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6" data-announcement-id="{{ $announcement->id }}">
                            <a href="{{ route('hr.announcement.show', $announcement->id) }}" class="block group">
                                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col sm:flex-row
                                    @if($announcement->category === 'important') border-l-4 border-red-500
                                    @elseif($announcement->category === 'event') border-l-4 border-blue-500
                                    @elseif($announcement->category === 'notice') border-l-4 border-yellow-500
                                    @elseif($announcement->category === 'job_offers') border-l-4 border-green-500
                                    @elseif($announcement->category === 'scholarship') border-l-4 border-purple-500
                                    @else border-l-4 border-green-500 @endif" 
                                    role="article" aria-labelledby="announcement-{{ $announcement->id }}">
                                    
                                    @php
                    $heroMedia = null;
                    $attachments = $announcement->attachments ?? [];
                    if(is_array($attachments) && count($attachments) > 0) {
                        foreach($attachments as $attachment) {
                            if(isset($attachment['mime_type']) && (str_contains($attachment['mime_type'], 'video') || str_contains($attachment['mime_type'], 'image'))) {
                                $heroMedia = $attachment;
                                break;
                            }
                        }
                    }
                @endphp
                                    
                                    <!-- Thumbnail Section -->
                                    <div class="relative w-full sm:w-80 bg-gray-200 aspect-video flex-shrink-0">
                                        @if($heroMedia)
                                            @if(str_contains($heroMedia['mime_type'], 'video'))
                                                <video class="w-full h-full object-cover" preload="metadata">
                                                    <source src="{{ Storage::url($heroMedia['path']) }}" type="{{ $heroMedia['mime_type'] }}">
                                                </video>
                                                <!-- Video Play Overlay -->
                                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 group-hover:bg-opacity-30 transition-all duration-300">
                                                    <div class="bg-red-600 text-white rounded-full p-3">
                                                        <svg class="w-6 h-6 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M8 5v14l11-7z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <!-- Duration Badge -->
                                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-80 text-white px-2 py-1 rounded text-xs font-medium">
                                                    Video
                                                </div>
                                            @else
                                                <img 
                                                    src="{{ Storage::url($heroMedia['path']) }}" 
                                                    alt="{{ $heroMedia['original_name'] }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @endif
                                        @else
                                            <!-- Default thumbnail for text-only announcements -->
                                            <img 
                                                src="/Images/bantay.jpg?v={{ time() }}" 
                                                alt="School Building"
                                                class="w-full h-full object-cover"
                                            >
                                        @endif
                                    </div>
                                
                                    <!-- Content Section -->
                                    <div class="p-4 flex-1 min-w-0">
                                        <h2 id="announcement-{{ $announcement->id }}" class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors duration-300 line-clamp-2">
                                            {{ $announcement->title }}
                                        </h2>
                                        
                                        <!-- Metadata -->
                                        <div class="flex items-center text-sm text-gray-500 mb-3 space-x-4 flex-wrap">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ $announcement->created_at->format('M j, Y') }}
                                            </span>
                                            
                                            @if($announcement->category)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white
                                                        @if($announcement->category === 'important') bg-red-500
                                                        @elseif($announcement->category === 'event') bg-blue-500
                                                        @elseif($announcement->category === 'notice') bg-yellow-500
                                                        @elseif($announcement->category === 'job_offers') bg-green-500
                                                        @elseif($announcement->category === 'scholarship') bg-purple-500
                                                        @else bg-gray-500 @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $announcement->category)) }}
                                                    </span>
                                                </span>
                                            @endif
                                            
                                            @php
                                                $attachments = $announcement->attachments ?? [];
                                            @endphp
                                            @if(is_array($attachments) && count($attachments) > 0)
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ count($attachments) }} {{ count($attachments) === 1 ? 'attachment' : 'attachments' }}
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Content Preview -->
                                        <p class="text-gray-600 text-sm line-clamp-3">
                                            {{ Str::limit(strip_tags($announcement->content), 150) }}
                                        </p>
                                    </div>
                                </article>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 bg-white rounded-xl shadow-lg">
                    <div class="max-w-md mx-auto">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V8m10 0H7"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No announcements yet</h3>
                        <p class="text-gray-500">Check back later for new announcements and updates.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection