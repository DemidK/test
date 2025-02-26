<div class="flex items-center space-x-4">
    <!-- Dynamic Navigation Links Container -->
    <div class="flex space-x-4" id="nav-links">
        @foreach($navLinks as $link)
            <a href="{{ $link->url }}" 
               id="link{{ $link->id }}" 
               class="text-gray-700 hover:text-gray-900 px-3 py-2 draggable" 
               draggable="true">
                {{ $link->name }}
            </a>
        @endforeach
    </div>
    <!-- Static Navigation Items -->
    <div class="flex space-x-4">
        <!-- Transportation Orders Dropdown -->
        <div class="relative group">
            <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                Transportation
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                <a href="{{ route('transportation_orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <span class="text-base">All Orders</span>
                </a>
                <a href="{{ route('transportation_orders.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <span class="text-base">New Order</span>
                </a>
            </div>
        </div>

        <!-- partners Dropdown -->
        <div class="relative group">
            <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                Partners
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                <a href="{{ route('partners.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <span class="text-base">All Partners</span>
                </a>
                <a href="{{ route('partners.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <span class="text-base">New Partner</span>
                </a>
            </div>
        </div>

        <!-- Invoices Dropdown -->
        <div class="relative group">
            <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                Rēķini
                <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                <a href="{{ route('invoices.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <span class="text-base">All</span>
                </a>
                <a href="{{ route('invoices.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <span class="text-base">New</span>
                </a>
            </div>
        </div>

        <!-- Authentication Links -->
        @auth
            <div class="relative group">
                <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                    {{ Auth::user()->name }}
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="absolute right-0 hidden bg-white shadow-lg rounded-lg w-48 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                    <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        Vadības panelis
                    </a>
                    <a href="{{ route('configs.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i>Configuration Settings
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Pieslēgties</a>
            <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Reģistrēties</a>
        @endauth
    </div>
</div>