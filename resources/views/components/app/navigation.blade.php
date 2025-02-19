<div class="flex items-center space-x-4">
    <!-- Draggable Navigation Links -->
    <div class="flex space-x-4" id="nav-links">
        @foreach ($navLinks as $link)
            <a href="{{ $link->url }}" 
               id="link{{ $link->id }}" 
               class="text-gray-700 hover:text-gray-900 px-3 py-2 draggable"
               draggable="true">
                {{ $link->name }}
            </a>
        @endforeach
    </div>

    <!-- Static Menu Items -->
    <div class="flex space-x-4">
        <!-- Invoices Dropdown -->
        <div class="relative group">
            <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                Invoices
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                <div class="relative pt-2">
                    <a href="{{ route('invoices.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <span class="text-base">All</span>
                    </a>
                    <a href="{{ route('invoices.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <span class="text-base">New</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Clients Dropdown -->
        <div class="relative group">
            <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                Clients
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                <div class="relative pt-2">
                    <a href="{{ route('clients.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <span class="text-base">All</span>
                    </a>
                    <a href="{{ route('clients.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <span class="text-base">New</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Authentication Links -->
        @guest
            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Login</a>
            <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Register</a>
        @else
            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Dashboard</a>
            <span class="text-gray-700 px-3 py-2">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 py-2">Logout</button>
            </form>
        @endguest
    </div>
</div>