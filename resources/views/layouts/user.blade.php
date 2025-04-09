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
                        <!-- Close Button -->
                        <button @click="open = false" class="p-2 rounded-md text-blue-100 hover:bg-blue-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Nav Links -->
                    <nav class="mt-4">
                        <x-side-nav-link href="{{ route('user.dashboard') }}" :active="request()->routeIs('user.dashboard')">
                            Dashboard
                        </x-side-nav-link>
                        <x-side-nav-link href="{{ route('user.profile.edit') }}" :active="request()->routeIs('user.profile.edit')">
                            Profile
                        </x-side-nav-link>
                        <x-side-nav-link href="{{ route('user.announcement') }}" :active="request()->routeIs('user.announcement')">
                            Announcement
                        </x-side-nav-link>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 transition-all duration-300">
                <nav class="bg-blue-900 shadow-lg">
                    <div class="mx-auto px-2 sm:px-6 lg:px-8">
                        <div class="relative flex items-center justify-between md:justify-end h-16">
                            <div class="absolute inset-y-0 left-0 flex items-center">
                                <!-- Sidebar Toggle Button -->
                                <button @click="open = !open" class="p-2 rounded-md text-blue-100 hover:bg-blue-700 focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                    </svg>
                                </button>
                            </div>
                            <div class="absolute inset-y-0 right-0 flex items-center">
                                <x-dropdown align="right" width="48">
                                    <x-slot name="trigger">
                                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium text-blue-100 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-200 p-2 rounded-md">
                                            <div>{{ Auth::user()->fullname }}</div>
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
                    </div>
                </nav>
                <div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>