import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Ensure DOM is fully loaded before starting Alpine
document.addEventListener('DOMContentLoaded', function() {
    Alpine.start();
});
