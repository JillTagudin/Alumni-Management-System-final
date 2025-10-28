<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $announcement->title }} - BCP Alumni</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ Str::limit(strip_tags($announcement->content), 160) }}">
    <meta name="keywords" content="BCP Alumni, {{ $announcement->category }}, announcement">
    
    <!-- Open Graph Meta Tags for Facebook -->
    <meta property="og:title" content="{{ $announcement->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($announcement->content), 200) }}">
    <meta property="og:url" content="{{ route('announcements.public', $announcement->id) }}">
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="BCP Alumni Network">
    @php
                $attachments = $announcement->attachments ?? [];
                $hasImage = false;
                if(is_array($attachments) && count($attachments) > 0) {
                    foreach($attachments as $attachment) {
                        if(isset($attachment['mime_type']) && str_contains($attachment['mime_type'], 'image')) {
                            echo '<meta property="og:image" content="' . Storage::url($attachment['path']) . '">';
                            $hasImage = true;
                            break;
                        }
                    }
                }
                if(!$hasImage) {
                    echo '<meta property="og:image" content="' . asset('/Images/bantay.jpg') . '">';
                }
            @endphp
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $announcement->title }}">
    <meta name="twitter:description" content="{{ Str::limit(strip_tags($announcement->content), 200) }}">
    @php
                $attachments = $announcement->attachments ?? [];
                $hasTwitterImage = false;
                if(is_array($attachments) && count($attachments) > 0) {
                    foreach($attachments as $attachment) {
                        if(isset($attachment['mime_type']) && str_contains($attachment['mime_type'], 'image')) {
                            echo '<meta name="twitter:image" content="' . Storage::url($attachment['path']) . '">';
                            $hasTwitterImage = true;
                            break;
                        }
                    }
                }
                if(!$hasTwitterImage) {
                    echo '<meta name="twitter:image" content="' . asset('/Images/bantay.jpg') . '">';
                }
            @endphp
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .social-btn {
            transition: all 0.3s ease;
        }
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-4xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('/logo/bcp.png') }}" alt="BCP Logo" class="h-10 w-10">
                    <div>
                        <h1 class="text-lg font-semibold text-gray-900">BCP Alumni Network</h1>
                        <p class="text-sm text-gray-600">Public Announcement</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 py-8">
        <!-- Announcement Card -->
        <article class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Media Section -->
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
            
            @if($heroMedia)
                <div class="aspect-video bg-black">
                    @if(str_contains($heroMedia['mime_type'], 'video'))
                        <video class="w-full h-full object-cover" controls preload="metadata">
                            <source src="{{ Storage::url($heroMedia['path']) }}" type="{{ $heroMedia['mime_type'] }}">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <img src="{{ Storage::url($heroMedia['path']) }}" alt="{{ $heroMedia['original_name'] }}" class="w-full h-full object-cover">
                    @endif
                </div>
            @else
                <div class="aspect-video bg-black">
                    <img src="{{ asset('/Images/bantay.jpg') }}" alt="BCP Campus" class="w-full h-full object-cover">
                </div>
            @endif

            <!-- Content Section -->
            <div class="p-6">
                <!-- Title and Meta -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ $announcement->title }}</h1>
                    <div class="flex items-center text-sm text-gray-600 space-x-4">
                        <time datetime="{{ $announcement->created_at->toISOString() }}" class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $announcement->created_at->format('F j, Y') }}
                        </time>
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

                <!-- Announcement Content -->
                <div class="prose prose-lg max-w-none mb-8">
                    {!! $announcement->content !!}
                </div>

                <!-- Social Sharing Section -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Share this announcement</h3>
                    <div class="flex flex-wrap gap-3">
                        <!-- Facebook Share -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('announcements.public', $announcement->id)) }}" 
                           target="_blank" 
                           class="social-btn inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>

                        <!-- Twitter/X Share -->
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('announcements.public', $announcement->id)) }}&text={{ urlencode($announcement->title) }}" 
                           target="_blank" 
                           class="social-btn inline-flex items-center px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            X (Twitter)
                        </a>


                        <!-- Copy Link -->
                        <button onclick="copyToClipboard()" 
                                class="social-btn inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Copy Link
                        </button>
                    </div>
                </div>

                <!-- Additional Files -->
                @php
                    $attachments = $announcement->attachments ?? [];
                    $nonMediaAttachments = [];
                    if(is_array($attachments)) {
                        foreach($attachments as $attachment) {
                            if(!str_contains($attachment['mime_type'], 'video') && !str_contains($attachment['mime_type'], 'image')) {
                                $nonMediaAttachments[] = $attachment;
                            }
                        }
                    }
                @endphp
                @if(count($nonMediaAttachments) > 0)
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Files</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($nonMediaAttachments as $attachment)
                                <a href="{{ Storage::url($attachment['path']) }}" 
                                   target="_blank" 
                                   class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['original_name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </article>

        <!-- Back to Alumni Network -->
        <div class="mt-8 text-center">
            <a href="https://www.facebook.com/profile.php?id=61577988277069" target="_blank" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Visit BCP Alumni Network
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-4xl mx-auto px-4 py-6">
            <div class="text-center text-sm text-gray-600">
                <p>&copy; {{ date('Y') }} BCP Alumni Network. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function copyToClipboard() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(function() {
                // Show success message
                const button = event.target.closest('button');
                const originalText = button.innerHTML;
                button.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Copied!
                `;
                button.classList.remove('bg-gray-600', 'hover:bg-gray-700');
                button.classList.add('bg-green-600', 'hover:bg-green-700');
                
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-gray-600', 'hover:bg-gray-700');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy link. Please copy manually: ' + url);
            });
        }
    </script>
</body>
</html>