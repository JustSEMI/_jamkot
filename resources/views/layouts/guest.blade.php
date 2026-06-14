<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PREVENT FOUC & SETUP UI THEME -->
    <script>
        (function() {
            const uiVersion = localStorage.getItem('jamkot-ui-version') || 'v1';
            document.documentElement.setAttribute('data-ui-version', uiVersion);
        })();
    </script>
    
    <title>@yield('title') | JAMKOT</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v={{ filemtime(public_path('css/auth.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/pages/mobile.css') }}?v={{ filemtime(public_path('css/pages/mobile.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/themes/material3.css') }}?v={{ filemtime(public_path('css/themes/material3.css')) }}">
    
    <!-- Page Specific Styles -->
    @stack('styles')
    
    @vite('resources/js/app.js')
</head>

<body>

    @yield('content')

    <!-- Page Specific Scripts -->
    @stack('scripts')

</body>

</html>
