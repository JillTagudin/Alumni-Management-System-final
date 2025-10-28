<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="relative min-h-screen flex" x-data="{ open: true }">
            <!-- Sidebar -->
            <aside :class="open ? 'w-64' : 'w-0 md:w-0'" 
                class="bg-blue-800 text-blue-100 transform transition-all duration-300 ease-in-out shadow-lg relative overflow-hidden">
                <!-- Sidebar Content -->
                <div x-show="open" x-transition.opacity.delay.200ms class="absolute top-0 left-0 w-64 px-2 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <a href="#">
                                <x-application-logo class="block h-9 w-auto fill-current text-blue-100"/>
                            </a>  
                            <span class="text-2xl font-extrabold">Alumni</span>
                        </div>
                    </div>
                    
                    <!-- Navigation Links -->
                    <nav class="mt-8">
                        <x-side-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                            {{ __('Dashboard') }}
                        </x-side-nav-link>
                        <x-side-nav-link :href="route('user.profile.edit')" :active="request()->routeIs('user.profile.edit')">
                            {{ __('Profile') }}
                        </x-side-nav-link>
                        <x-side-nav-link :href="route('user.announcement')" :active="request()->routeIs('user.announcement')">
                            {{ __('Announcement') }}
                        </x-side-nav-link>
                        <x-side-nav-link :href="route('user.membership.index')" :active="request()->routeIs('user.membership.*')">
                            {{ __('Membership') }}
                        </x-side-nav-link>
                        <x-side-nav-link :href="route('user.feedback.index')" :active="request()->routeIs('user.feedback.*')">
                            {{ __('Feedback') }}
                        </x-side-nav-link>
                        <x-side-nav-link :href="route('user.alumni-concerns.index')" :active="request()->routeIs('user.alumni-concerns.*')">
                            {{ __('Alumni Concerns') }}
                        </x-side-nav-link>
                    </nav>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navigation -->
                <nav class="bg-white border-b border-gray-200 px-4 py-3">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <!-- Sidebar Toggle -->
                            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:text-gray-700 mr-4">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>

                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('user.profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </nav>
                
                <!-- Header Section -->
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
                
                <!-- Main Content -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>