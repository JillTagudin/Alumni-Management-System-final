@extends('layouts.admin')

@section('title', $announcement->title)

@section('content')
<div class="min-h-screen bg-gray-50">
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
                        $firstDecode = json_decode($rawAttachments, true);
                        if (is_string($firstDecode)) {
                            $attachments = json_decode($firstDecode, true) ?? [];
                        } elseif (is_array($firstDecode)) {
                            $attachments = $firstDecode;
                        }
                    } elseif (is_array($rawAttachments)) {
                        $attachments = $rawAttachments;
                    }
                    
                    if(is_array($attachments) && count($attachments) > 0) {
                        foreach($attachments as $attachment) {
                            if(isset($attachment['mime_type']) && (str_contains($attachment['mime_type'], 'video') || str_contains($attachment['mime_type'], 'image'))) {
                                if(!$heroMedia) {
                                    $heroMedia = $attachment;
                                } else {
                                    $otherAttachments[] = $attachment;
                                    if(!$fallbackImage && str_contains($attachment['mime_type'], 'image')) {
                                        $fallbackImage = $attachment;
                                    }
                                }
                            } else {
                                $otherAttachments[] = $attachment;
                            }
                        }
                    }
                @endphp

                <!-- Hero Media Section -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-4">
                    @if($heroMedia)
                        @if(str_contains($heroMedia['mime_type'], 'video'))
                            <div class="relative aspect-video bg-black">
                                <video 
                                    controls 
                                    class="w-full h-full"
                                    poster="{{ $fallbackImage ? Storage::url($fallbackImage['path']) : '/Images/bantay.jpg' }}"
                                >
                                    <source src="{{ Storage::url($heroMedia['path']) }}" type="{{ $heroMedia['mime_type'] }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @elseif(str_contains($heroMedia['mime_type'], 'image'))
                            <div class="relative aspect-video bg-black group cursor-pointer" onclick="openImageModal('{{ Storage::url($heroMedia['path']) }}')">
                                <img 
                                    src="{{ Storage::url($heroMedia['path']) }}" 
                                    alt="{{ $heroMedia['name'] ?? $heroMedia['original_name'] ?? 'Announcement Image' }}"
                                    class="w-full h-full object-cover"
                                >
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                                    <button class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 px-4 py-2 rounded-lg flex items-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                        View
                                    </button>
                                </div>
                            </div>
                        @endif
                    @else
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
                <div class="bg-white rounded-lg shadow-sm p-6 mb-4">
                    <h1 class="text-2xl font-bold text-gray-900 mb-4">{{ $announcement->title }}</h1>
                    
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-8 0H6a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-2"></path>
                            </svg>
                            Published {{ $announcement->created_at->format('M j, Y') }}
                        </div>
                        
                        @if($announcement->category)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $announcement->category }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Content -->
                    <div class="prose prose-gray max-w-none">
                        {!! $announcement->content !!}
                    </div>
                </div>

                <!-- Additional Media Grid -->
                @if(count($otherAttachments) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Files</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($otherAttachments as $attachment)
                                <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
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
                                            {{ $attachment['name'] ?? $attachment['original_name'] ?? 'Unknown File' }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ isset($attachment['size']) ? number_format($attachment['size'] / 1024, 1) . ' KB' : 'Unknown size' }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ Storage::url($attachment['path']) }}" 
                                           download="{{ $attachment['name'] ?? $attachment['original_name'] ?? 'file' }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Actions</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <a href="{{ route('hr.announcements') }}" 
                           class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Announcements
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection