<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <nav class="bg-white shadow-lg mb-8" x-data="{ mobileMenuOpen: false }">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="text-xl font-bold">test</a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-gray-900 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center">
                    @include('components.app.navigation')
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div x-show="mobileMenuOpen" class="md:hidden pb-4">
                <!-- Dynamic Navigation Links -->
                @foreach($navLinks as $link)
                    <a href="{{ $link->url }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        {{ $link->name }}
                    </a>
                @endforeach

                <!-- Invoices Dropdown -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        Rēķini
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4">
                        <a href="{{ url('/invoices') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Visi rēķini</a>
                        <a href="{{ url('/invoices/create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Jauns rēķins</a>
                    </div>
                </div>

                <!-- partners Dropdown -->
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        Partneri
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4">
                        <a href="{{ url('/partners') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Visi partneri</a>
                        <a href="{{ url('/partners/create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Jauns partneris</a>
                    </div>
                </div>

                <!-- Authentication Links -->
                @auth
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                            {{ Auth::user()->name }}
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" class="pl-4">
                            <a href="/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Vadības panelis</a>
                            <a href="{{ route('configs.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Konfigurācijas iestatījumi
                            </a>
                            <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profils</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Izrakstīties</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Pieslēgties</a>
                    <a href="{{ route('register') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Reģistrēties</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4">
        @yield('content')
    </main>

    <footer class="bg-white shadow-lg mt-8">
        <div class="container mx-auto px-4 py-4 text-center text-gray-700">
            © {{ date('Y') }} test. Visas tiesības aizsargātas.
        </div>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('js/nav.js') }}"></script>
</body>
</html>