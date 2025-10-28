@extends('layouts.user')

@section('title', $announcement->title)

@section('content')
<div class="py-8 min-h-screen" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset('Images/bcp-building.jpg') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed;">
    <!-- YouTube-style Detail Page -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Area -->
            <div class="lg:col-span-2">
                @php
                    $heroMedia = null;
                    $fallbackImage = null;
                    $otherAttachments = [];
                    
                    // Handle double-encoded JSON attachments
                    $rawAttachments = $announcement->getAttributes()['attachments'] ?? '[]';
                    $attachments = [];
                    
                    if (is_string($rawAttachments)) {
                        // Try first decode
                        $firstDecode = json_decode($rawAttachments, true);
                        if (is_string($firstDecode)) {
                            // Double-encoded JSON, decode again
                            $attachments = json_decode($firstDecode, true) ?? [];
                        } elseif (is_array($firstDecode)) {
                            // Single-encoded JSON
                            $attachments = $firstDecode;
                        }
                    } elseif (is_array($rawAttachments)) {
                        // Already an array (model casting worked)
                        $attachments = $rawAttachments;
                    }
                    
                    if(is_array($attachments) && count($attachments) > 0) {
                        foreach($attachments as $attachment) {
                            if(isset($attachment['mime_type']) && (str_contains($attachment['mime_type'], 'video') || str_contains($attachment['mime_type'], 'image'))) {
                                if(!$heroMedia) {
                                    $heroMedia = $attachment;
                                } else {
                                    $otherAttachments[] = $attachment;
                                    // Store first image as fallback if no hero media is video
                                    if(!$fallbackImage && str_contains($attachment['mime_type'], 'image')) {
                                        $fallbackImage = $attachment;
                                    }
                                }
                            } else {
                                $otherAttachments[] = $attachment;
                            }
                        }
                        
                        // If hero media is not found, look for any image as fallback
                        if(!$heroMedia) {
                            foreach($attachments as $attachment) {
                                if(isset($attachment['mime_type']) && str_contains($attachment['mime_type'], 'image')) {
                                    $heroMedia = $attachment;
                                    break;
                                }
                            }
                        }
                    }
                @endphp
                
                <!-- Media Player Section -->
                <div class="bg-black rounded-lg overflow-hidden shadow-lg mb-4">
                    @if($heroMedia)
                        @if(str_contains($heroMedia['mime_type'], 'video'))
                            <!-- Video Player -->
                            <div class="relative aspect-video">
                                <video 
                                    class="w-full h-full" 
                                    controls 
                                    preload="metadata"
                                    poster=""
                                    id="main-video"
                                >
                                    <source src="{{ Storage::url($heroMedia['path']) }}" type="{{ $heroMedia['mime_type'] }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @else
                            <!-- Image Display -->
                            <div class="relative aspect-video bg-black flex items-center justify-center group">
                                <img 
                                    id="announcement-image"
                                    src="{{ Storage::url($heroMedia['path']) }}" 
                                    alt="{{ $heroMedia['name'] ?? 'Announcement Image' }}"
                                    class="max-w-full max-h-full object-contain cursor-pointer"
                                    onclick="openImageModal('{{ Storage::url($heroMedia['path']) }}', '{{ $heroMedia['name'] ?? 'Announcement Image' }}')"
                                >
                                <!-- View/Zoom Button Overlay -->
                                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <button 
                                        onclick="openImageModal('{{ Storage::url($heroMedia['path']) }}', '{{ $heroMedia['name'] ?? 'Announcement Image' }}')"
                                        class="bg-black bg-opacity-50 text-white px-3 py-2 rounded-lg hover:bg-opacity-70 transition-all duration-200 flex items-center gap-2"
                                        title="View Full Size"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                        View
                                    </button>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Default school building image for announcements without media -->
                        <div class="relative aspect-video bg-black flex items-center justify-center">
                            <img 
                                src="/Images/bantay.jpg?v={{ time() }}" 
                                alt="School Building"
                                class="w-full h-full object-cover"
                            >
                        </div>
                    @endif
                </div>
                
                <!-- Title and Metadata -->
                <div class="bg-white rounded-lg p-6 shadow-sm mb-4">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2 leading-tight">
                        {{ $announcement->title }}
                    </h1>
                    <div class="flex items-center text-sm text-gray-600 space-x-4">
                        <time datetime="{{ $announcement->created_at->toISOString() }}" class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $announcement->created_at->format('M j, Y') }}
                        </time>
                        @php
                            $attachments = $announcement->attachments ?? [];
                        @endphp
                        @if(is_array($attachments) && count($attachments) > 0)
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                {{ count($attachments) }} file{{ count($attachments) > 1 ? 's' : '' }}
                            </span>
                        @endif
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($announcement->category === 'important') bg-red-500 text-white
                            @elseif($announcement->category === 'event') bg-blue-500 text-white
                            @elseif($announcement->category === 'notice') bg-yellow-500 text-white
                            @else bg-gray-500 text-white
                            @endif">
                            {{ ucfirst($announcement->category) }}
                        </span>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="bg-white rounded-lg p-6 shadow-sm mb-6">
                    <div class="prose prose-gray max-w-none">
                        {!! $announcement->content !!}
                    </div>
                </div>
                
                <!-- Additional Media Grid -->
                @if(count($otherAttachments) > 0)
                    <div class="bg-white rounded-lg p-6 shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Additional Files
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($otherAttachments as $attachment)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex-shrink-0 mr-3">
                                        @if(str_contains($attachment['mime_type'], 'image'))
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @elseif(str_contains($attachment['mime_type'], 'video'))
                                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @elseif(str_contains($attachment['mime_type'], 'audio'))
                                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $attachment['original_name'] }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ strtoupper(pathinfo($attachment['original_name'], PATHINFO_EXTENSION)) }} • 
                                            {{ number_format($attachment['size'] / 1024, 1) }} KB
                                        </p>
                                    </div>
                                    <a href="{{ Storage::url($attachment['path']) }}" 
                                       download="{{ $attachment['original_name'] }}"
                                       class="ml-2 text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                       aria-label="Download {{ $attachment['original_name'] }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Back to Announcements -->
                <div class="bg-white rounded-lg p-4 shadow-sm mb-6">
                    <a href="{{ route('user.announcement') }}" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Announcements
                    </a>
                </div>
                
                <!-- Announcement Info -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <h3 class="font-semibold text-gray-900 mb-3">Announcement Details</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Published:</span>
                            <span class="text-gray-900">{{ $announcement->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                        <span class="text-gray-600">Category:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white
                            @if($announcement->category === 'important') bg-red-500
                            @elseif($announcement->category === 'event') bg-blue-500
                            @elseif($announcement->category === 'notice') bg-yellow-500
                            @elseif($announcement->category === 'job_offers') bg-green-500
                            @elseif($announcement->category === 'scholarship') bg-purple-500
                            @else bg-gray-500 @endif">
                            {{ ucfirst(str_replace('_', ' ', $announcement->category)) }}
                        </span>
                    </div>
                        @php
                            $sidebarAttachments = $announcement->attachments ?? [];
                        @endphp
                        @if(is_array($sidebarAttachments) && count($sidebarAttachments) > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Attachments:</span>
                                <span class="text-gray-900">{{ count($sidebarAttachments) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Social Sharing Section -->
                <div class="bg-white rounded-lg p-4 shadow-sm mt-6">
                    <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Share this announcement
                    </h3>
                    <div class="space-y-2">
                        @php
                            $shareUrl = route('announcements.public', $announcement->id);
                            $shareText = urlencode($announcement->title);
                        @endphp
                        
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" 
                           target="_blank" 
                           class="flex items-center w-full px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                        
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ $shareText }}" 
                           target="_blank" 
                           class="flex items-center w-full px-3 py-2 text-sm font-medium text-white bg-sky-500 rounded-md hover:bg-sky-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            X (Twitter)
                        </a>
                        

                        <button onclick="copyToClipboard('{{ $shareUrl }}')" 
                                class="flex items-center w-full px-3 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create a temporary notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
        notification.textContent = 'Link copied to clipboard!';
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 3000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy link to clipboard');
    });
}

// Image Modal Functions
function openImageModal(imageSrc, imageAlt) {
    // Create modal HTML
    const modalHTML = `
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
            <div class="relative max-w-full max-h-full">
                <button 
                    onclick="closeImageModal()" 
                    class="absolute top-4 right-4 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-70 transition-all duration-200 z-10"
                    title="Close"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img 
                    src="${imageSrc}" 
                    alt="${imageAlt}"
                    class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
                    style="max-height: 90vh; max-width: 90vw;"
                >
                <div class="absolute bottom-4 left-4 right-4 text-center">
                    <p class="text-white bg-black bg-opacity-50 px-4 py-2 rounded-lg inline-block">
                        ${imageAlt}
                    </p>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
    
    // Close modal on background click
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
    
    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = 'auto';
    }
}
</script>
@endsection