@extends('layouts.user')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Announcements') }}
    </h2>
@endsection

@section('content')
    <div class="min-h-screen relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Real-time Announcement Notifications -->
            <div id="new-announcement-notification" class="hidden bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10A8 8 0 11-16 0 8 8 0 0118 10zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H15a.75.75 0 000-1.5h-4.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong id="new-announcement-count">0</strong> new announcement(s) available.
                                <button onclick="refreshAnnouncements()" class="font-medium underline text-blue-700 hover:text-blue-600 ml-2">
                                    Refresh to see them
                                </button>
                            </p>
                        </div>
                    </div>
                    <button onclick="hideNewAnnouncementNotification()" class="text-blue-400 hover:text-blue-600">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            
            @if($announcements->count() > 0)
                <!-- YouTube-style Single Column Layout -->
                <div class="max-w-4xl mx-auto space-y-6">
                    @foreach($announcements as $announcement)
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6" data-announcement-id="{{ $announcement->id }}">
                            <a href="{{ route('user.announcement.show', $announcement->id) }}" class="block group">
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
    
    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4" onclick="closeImageModal()">
        <div class="relative max-w-4xl max-h-full">
            <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors duration-200">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <script>
        // Real-time announcement checking
        let announcementCheckInterval;
        let lastAnnouncementCheck = new Date().toISOString();
        let isOnline = navigator.onLine;
        let isVisible = !document.hidden;
        
        function checkForNewAnnouncements() {
            if (!isOnline || !isVisible) return;
            
            fetch(`/api/latest-announcements?last_check=${encodeURIComponent(lastAnnouncementCheck)}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success && data.count > 0) {
                    showNewAnnouncementNotification(data.count);
                    lastAnnouncementCheck = data.timestamp;
                }
            })
            .catch(error => {
                console.error('Error checking for new announcements:', error);
            });
        }
        
        function showNewAnnouncementNotification(count) {
            const notification = document.getElementById('new-announcement-notification');
            const countElement = document.getElementById('new-announcement-count');
            
            if (notification && countElement) {
                countElement.textContent = count;
                notification.classList.remove('hidden');
            }
        }
        
        function hideNewAnnouncementNotification() {
            const notification = document.getElementById('new-announcement-notification');
            if (notification) {
                notification.classList.add('hidden');
            }
        }
        
        function refreshAnnouncements() {
            window.location.reload();
        }
        
        function startAnnouncementChecking() {
            if (announcementCheckInterval) clearInterval(announcementCheckInterval);
            
            // Check immediately
            checkForNewAnnouncements();
            
            // Set up regular checks every 30 seconds
            announcementCheckInterval = setInterval(checkForNewAnnouncements, 30000);
        }
        
        function stopAnnouncementChecking() {
            if (announcementCheckInterval) {
                clearInterval(announcementCheckInterval);
                announcementCheckInterval = null;
            }
        }
        
        function handleVisibilityChange() {
            isVisible = !document.hidden;
            if (isVisible && isOnline) {
                startAnnouncementChecking();
            } else {
                stopAnnouncementChecking();
            }
        }
        
        function handleOnlineStatus() {
            isOnline = navigator.onLine;
            if (isOnline && isVisible) {
                startAnnouncementChecking();
            } else {
                stopAnnouncementChecking();
            }
        }
        
        function toggleVideo(videoId) {
            const video = document.getElementById(videoId);
            const playBtn = video.parentElement.querySelector('.play-btn');
            
            if (video.paused) {
                video.play();
                playBtn.style.display = 'none';
            } else {
                video.pause();
                playBtn.style.display = 'flex';
            }
        }
        
        function openImageModal(src, alt) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            modalImage.src = src;
            modalImage.alt = alt;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
        
        // Auto-hide video controls after play
        document.querySelectorAll('video').forEach(video => {
            video.addEventListener('play', function() {
                const playBtn = this.parentElement.querySelector('.play-btn');
                if (playBtn) playBtn.style.display = 'none';
            });
            
            video.addEventListener('pause', function() {
                const playBtn = this.parentElement.querySelector('.play-btn');
                if (playBtn) playBtn.style.display = 'flex';
            });
        });
        
        // Add this function to mark announcements as read when viewed
        function markAnnouncementAsRead(announcementId) {
            fetch(`/user/announcement/${announcementId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide notification if no more unread announcements
                    checkForNewAnnouncements();
                }
            })
            .catch(error => {
                console.error('Error marking announcement as read:', error);
            });
        }
        
        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Event listeners
            document.addEventListener('visibilitychange', handleVisibilityChange);
            window.addEventListener('online', handleOnlineStatus);
            window.addEventListener('offline', handleOnlineStatus);
            window.addEventListener('beforeunload', stopAnnouncementChecking);
            
            // Start checking if online and visible
            if (isOnline && isVisible) {
                startAnnouncementChecking();
            }
            
            // Mark all visible announcements as read after a short delay
            setTimeout(() => {
                const announcementCards = document.querySelectorAll('[data-announcement-id]');
                announcementCards.forEach(card => {
                    const announcementId = card.getAttribute('data-announcement-id');
                    markAnnouncementAsRead(announcementId);
                });
                
                // Hide the notification banner since user is viewing announcements
                hideNewAnnouncementNotification();
            }, 2000); // 2 second delay to ensure user actually viewed the page
        });
    </script>
@endsection