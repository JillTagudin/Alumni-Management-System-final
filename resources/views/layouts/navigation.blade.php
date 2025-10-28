// ... existing code ...
    <x-nav-link :href="route('admin.announcement')" :active="request()->routeIs('admin.announcement')">
        {{ __('Announcement') }}
    </x-nav-link>

    
    <x-nav-link :href="route('AccountManagement.index')" :active="request()->routeIs('AccountManagement.index')">
        {{ __('Account Records') }}
    </x-nav-link>


  
    