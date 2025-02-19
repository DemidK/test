<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-white shadow-lg mb-8">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <x-app.logo />
                <x-app.navigation :nav-links="$navLinks" />
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-6">
        @yield('content')
    </main>

    <x-app.footer />

    <!-- Scripts -->
    <script src="{{ asset('js/nav.js') }}"></script>
</body>
</html>