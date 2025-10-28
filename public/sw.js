// Service Worker for Push Notifications
// ULTRAMAR Alumni Management System

const CACHE_NAME = 'ultramar-notifications-v1';
const urlsToCache = [
    '/',
    '/favicon.ico'
];

// Install event - cache resources
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                return cache.addAll(urlsToCache);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Push event - handle incoming push notifications
self.addEventListener('push', event => {
    console.log('Push event received:', event);
    
    let notificationData = {
        title: 'ULTRAMAR Notification',
        body: 'You have a new notification',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: 'default',
        requireInteraction: true,
        actions: [
            {
                action: 'view',
                title: 'View',
                icon: '/favicon.ico'
            },
            {
                action: 'dismiss',
                title: 'Dismiss'
            }
        ]
    };
    
    // Parse push data if available
    if (event.data) {
        try {
            const data = event.data.json();
            notificationData = { ...notificationData, ...data };
        } catch (e) {
            console.error('Error parsing push data:', e);
            notificationData.body = event.data.text() || notificationData.body;
        }
    }
    
    event.waitUntil(
        self.registration.showNotification(notificationData.title, {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            tag: notificationData.tag,
            requireInteraction: notificationData.requireInteraction,
            actions: notificationData.actions,
            data: notificationData.data || {}
        })
    );
});

// Notification click event - handle user interactions
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    if (event.action === 'dismiss') {
        // User dismissed the notification
        return;
    }
    
    // Default action or 'view' action - open/focus the app
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(clientList => {
                // Check if there's already a window/tab open
                for (const client of clientList) {
                    if (client.url.includes(self.location.origin) && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // If no window is open, open a new one
                if (clients.openWindow) {
                    return clients.openWindow('/');
                }
            })
    );
});

// Notification close event
self.addEventListener('notificationclose', event => {
    console.log('Notification closed:', event);
    // Track notification dismissal if needed
});

// Message event - handle messages from the main thread
self.addEventListener('message', event => {
    console.log('Service Worker received message:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

// Fetch event - handle network requests (basic caching strategy)
self.addEventListener('fetch', event => {
    // Only handle GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Skip non-HTTP requests
    if (!event.request.url.startsWith('http')) {
        return;
    }
    
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Return cached version or fetch from network
                return response || fetch(event.request);
            })
            .catch(() => {
                // Fallback for offline scenarios
                if (event.request.destination === 'document') {
                    return caches.match('/');
                }
            })
    );
});