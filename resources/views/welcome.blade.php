<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'BCP Alumni System') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .hero-bg {
            background-image: url('{{ asset('Images/bcp-campus.jpg.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }
        
        .bg-overlay {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.2) 100%);
        }
        
        .header-blur {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Header Navigation -->
    <header class="fixed top-0 left-0 right-0 z-50 header-blur border-b border-gray-200">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('logo/bcp.png') }}" alt="BCP Logo" class="h-12 w-12">
                    <div class="text-gray-800">
                        <h1 class="text-xl font-bold">BCP Alumni</h1>
                        <p class="text-sm opacity-70">Management System</p>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" 
                       class="px-6 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-300 font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300 font-medium shadow-lg">
                        Register
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <main class="min-h-screen hero-bg relative">
        <div class="bg-overlay absolute inset-0"></div>
        
        <div class="relative z-10 min-h-screen flex items-center justify-center pt-48">
            <div class="text-center text-white px-6 max-w-4xl mx-auto">
                <!-- Features Preview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-center">
                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-lg">
                        <div class="text-3xl mb-3">🎓</div>
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Alumni Network</h3>
                        <p class="text-sm text-gray-600">Connect with fellow graduates and expand your professional network</p>
                    </div>
                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-lg">
                        <div class="text-3xl mb-3">💼</div>
                        <h3 class="text-lg font-semibold mb-2 text-gray-800">Career Opportunities</h3>
                        <p class="text-sm text-gray-600">Discover job openings and career advancement opportunities</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Menu Script -->
    <script>
        // Add any interactive functionality here if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scrolling for any anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html>