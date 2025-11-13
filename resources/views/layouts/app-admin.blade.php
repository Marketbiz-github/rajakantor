<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - Dashboard</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
        <link rel="icon" type="image/png" href="{{ asset($siteSettings->favicon) }}">
        <!-- Font Awesome CDN v6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <style>
            body {
                font-family: 'Poppins', Arial, sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Sidebar component - will be fixed on desktop, hidden on mobile -->
            <x-sidebar-dashboard />
            
            <!-- Main Content - will be pushed right on desktop, full width on mobile -->
            <div class="lg:pl-72">
                <main class="p-4 sm:p-6">
                    <div class="px-4 sm:px-6 lg:px-8 mb-6">
                        @yield('content')
                    </div>

                    <!-- Flash Messages -->
                    <x-toast-messages />
                </main>
            </div>
        </div>

        @stack('scripts')
        @yield('scripts')
    </body>
</html>
