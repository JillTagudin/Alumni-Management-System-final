<!-- Notification Center Component -->
<div id="notification-center" class="fixed top-4 right-4 z-50 space-y-2 max-w-sm">
    <!-- Notifications will be dynamically added here -->
</div>



<!-- Notification Panel (Dropdown) -->
<div id="notification-panel" class="hidden fixed top-16 right-4 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
    <div class="p-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
            <button onclick="clearAllNotifications()" class="text-sm text-blue-600 hover:text-blue-800">
                Clear All
            </button>
        </div>
    </div>
    <div id="notification-list" class="divide-y divide-gray-200">
        <div class="p-4 text-center text-gray-500">
            No notifications
        </div>
    </div>
</div>

<style>
/* Notification animations */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.notification-enter {
    animation: slideInRight 0.3s ease-out;
}

.notification-exit {
    animation: slideOutRight 0.3s ease-in;
}

.notification-item {
    transition: all 0.2s ease;
}

.notification-item:hover {
    background-color: #f9fafb;
}
</style>

<script>
// Centralized Notification System
class NotificationCenter {
    constructor() {
        this.notifications = [];
        this.maxNotifications = 5;
        this.defaultDuration = 5000; // 5 seconds
        this.container = document.getElementById('notification-center');
        this.badge = document.getElementById('notification-badge');
        this.panel = document.getElementById('notification-panel');
        this.notificationList = document.getElementById('notification-list');
        
        // Close panel when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#notification-bell') && !e.target.closest('#notification-panel')) {
                this.hidePanel();
            }
        });
    }
    
    show(message, type = 'info', duration = null, persistent = false, enablePush = true) {
        const notification = {
            id: Date.now() + Math.random(),
            message,
            type,
            timestamp: new Date(),
            persistent
        };
        
        this.notifications.unshift(notification);
        this.updateBadge();
        this.addToPanel(notification);
        
        // Show push notification if tab is not active and push is enabled
        if (enablePush && pushNotificationManager && document.hidden) {
            const pushTitle = this.getPushTitle(type);
            const pushOptions = {
                body: message,
                tag: `notification-${notification.id}`,
                data: {
                    notificationId: notification.id,
                    type: type,
                    timestamp: notification.timestamp.toISOString()
                }
            };
            
            pushNotificationManager.showPushNotification(pushTitle, pushOptions);
        }
        
        // Create toast notification only if tab is visible
        if (!document.hidden) {
            const toast = this.createToast(notification);
            this.container.appendChild(toast);
            
            // Remove old notifications if exceeding max
            while (this.container.children.length > this.maxNotifications) {
                this.container.removeChild(this.container.firstChild);
            }
            
            // Auto-remove if not persistent
            if (!persistent) {
                setTimeout(() => {
                    this.removeToast(toast);
                }, duration || this.defaultDuration);
            }
        }
        
        return notification.id;
    }
    
    createToast(notification) {
        const toast = document.createElement('div');
        toast.className = `notification-item notification-enter bg-white rounded-lg shadow-lg border-l-4 p-4 mb-2 ${this.getTypeClasses(notification.type)}`;
        toast.dataset.id = notification.id;
        
        toast.innerHTML = `
            <div class="flex justify-between items-start">
                <div class="flex">
                    <div class="flex-shrink-0">
                        ${this.getTypeIcon(notification.type)}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">
                            ${notification.message}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            ${notification.timestamp.toLocaleTimeString()}
                        </p>
                    </div>
                </div>
                <button onclick="notificationCenter.removeToast(this.closest('.notification-item'))" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        `;
        
        return toast;
    }
    
    addToPanel(notification) {
        if (this.notificationList.children.length === 1 && this.notificationList.children[0].textContent.includes('No notifications')) {
            this.notificationList.innerHTML = '';
        }
        
        const item = document.createElement('div');
        item.className = 'p-4 hover:bg-gray-50 cursor-pointer';
        item.dataset.id = notification.id;
        
        item.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${this.getTypeIcon(notification.type)}
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-gray-900">${notification.message}</p>
                    <p class="text-xs text-gray-500 mt-1">${notification.timestamp.toLocaleString()}</p>
                </div>
            </div>
        `;
        
        this.notificationList.insertBefore(item, this.notificationList.firstChild);
        
        // Limit panel notifications
        while (this.notificationList.children.length > 10) {
            this.notificationList.removeChild(this.notificationList.lastChild);
        }
    }
    
    removeToast(toast) {
        if (toast && toast.parentElement) {
            toast.classList.add('notification-exit');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.parentElement.removeChild(toast);
                }
            }, 300);
        }
    }
    
    getTypeClasses(type) {
        const classes = {
            'success': 'border-green-400',
            'error': 'border-red-400',
            'warning': 'border-yellow-400',
            'info': 'border-blue-400'
        };
        return classes[type] || classes.info;
    }
    
    getTypeIcon(type) {
        const icons = {
            'success': '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
            'error': '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>',
            'warning': '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>',
            'info': '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>'
        };
        return icons[type] || icons.info;
    }
    
    updateBadge() {
        const count = this.notifications.length;
        if (this.badge) {
            this.badge.textContent = count;
            if (count > 0) {
                this.badge.classList.remove('hidden');
            } else {
                this.badge.classList.add('hidden');
            }
        }
    }
    
    showPanel() {
        if (this.panel) {
            this.panel.classList.remove('hidden');
        }
    }
    
    hidePanel() {
        if (this.panel) {
            this.panel.classList.add('hidden');
        }
    }
    
    getPushTitle(type) {
        const titles = {
            'success': 'Success',
            'error': 'Error',
            'warning': 'Warning',
            'info': 'ULTRAMAR Notification'
        };
        return titles[type] || titles.info;
    }
    
    clearAll() {
        this.notifications = [];
        this.updateBadge();
        
        // Clear toasts
        if (this.container) {
            this.container.innerHTML = '';
        }
        
        // Clear panel
        if (this.notificationList) {
            this.notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">No notifications</div>';
        }
    }
}

// Global notification center instance
let notificationCenter;

// Push Notification Manager
class PushNotificationManager {
    constructor() {
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.permission = Notification.permission;
        this.registration = null;
        this.init();
    }
    
    async init() {
        if (!this.isSupported) {
            console.warn('Push notifications are not supported in this browser');
            return;
        }
        
        try {
            // Register service worker
            this.registration = await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker registered successfully');
            
            // Request permission if not already granted
            if (this.permission === 'default') {
                await this.requestPermission();
            }
        } catch (error) {
            console.error('Service Worker registration failed:', error);
        }
    }
    
    async requestPermission() {
        try {
            this.permission = await Notification.requestPermission();
            return this.permission === 'granted';
        } catch (error) {
            console.error('Error requesting notification permission:', error);
            return false;
        }
    }
    
    async showPushNotification(title, options = {}) {
        if (!this.isSupported || this.permission !== 'granted') {
            return false;
        }
        
        // Only show push notification if tab is not visible
        if (!document.hidden) {
            return false;
        }
        
        const defaultOptions = {
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            vibrate: [200, 100, 200],
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
        
        const finalOptions = { ...defaultOptions, ...options };
        
        try {
            if (this.registration) {
                await this.registration.showNotification(title, finalOptions);
                return true;
            }
        } catch (error) {
            console.error('Error showing push notification:', error);
        }
        
        return false;
    }
    
    isPermissionGranted() {
        return this.permission === 'granted';
    }
}

// Global push notification manager instance
let pushNotificationManager;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    notificationCenter = new NotificationCenter();
    pushNotificationManager = new PushNotificationManager();
});

// Global functions
function toggleNotificationPanel() {
    if (notificationCenter.panel.classList.contains('hidden')) {
        notificationCenter.showPanel();
    } else {
        notificationCenter.hidePanel();
    }
}

function clearAllNotifications() {
    notificationCenter.clearAll();
}

// Global notification functions for easy access
function showNotification(message, type = 'info', duration = null, persistent = false, enablePush = true) {
    if (notificationCenter) {
        return notificationCenter.show(message, type, duration, persistent, enablePush);
    }
}

function showSuccess(message, duration = null, enablePush = true) {
    return showNotification(message, 'success', duration, false, enablePush);
}

function showError(message, duration = null, enablePush = true) {
    return showNotification(message, 'error', duration, false, enablePush);
}

function showWarning(message, duration = null, enablePush = true) {
    return showNotification(message, 'warning', duration, false, enablePush);
}

function showInfo(message, duration = null, enablePush = true) {
    return showNotification(message, 'info', duration, false, enablePush);
}

// Push notification specific functions
function requestNotificationPermission() {
    if (pushNotificationManager) {
        return pushNotificationManager.requestPermission();
    }
    return Promise.resolve(false);
}

function isNotificationPermissionGranted() {
    if (pushNotificationManager) {
        return pushNotificationManager.isPermissionGranted();
    }
    return false;
}
</script>