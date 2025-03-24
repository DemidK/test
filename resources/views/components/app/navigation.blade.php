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
        <x-permission permission="view_transportation_orders">
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
                    <x-permission permission="create_transportation_orders">
                        <a href="{{ route('transportation_orders.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">New Order</span>
                        </a>
                    </x-permission>
                </div>
            </div>
        </x-permission>

        <!-- Partners Dropdown -->
        <x-permission permission="view_partners">
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
                    <x-permission permission="create_partners">
                        <a href="{{ route('partners.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">New Partner</span>
                        </a>
                    </x-permission>
                </div>
            </div>
        </x-permission>

        <!-- Invoices Dropdown -->
        <x-permission permission="view_invoices">
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
                    <x-permission permission="create_invoices">
                        <a href="{{ route('invoices.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">New</span>
                        </a>
                    </x-permission>
                </div>
            </div>
        </x-permission>
        
        <!-- Permission Management Dropdown - Visible for superusers or users with specific permissions -->
        <x-permission permission="view_permissions">
            <div class="relative group">
                <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                    Permissions
                    <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="absolute hidden bg-white shadow-lg rounded-lg w-48 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                    <a href="{{ route('permissions.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <span class="text-base">All Permissions</span>
                    </a>
                    <x-permission permission="create_permissions">
                        <a href="{{ route('permissions.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">New Permission</span>
                        </a>
                    </x-permission>
                    <x-permission permission="view_roles">
                        <a href="{{ route('roles.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Manage Roles</span>
                        </a>
                    </x-permission>
                    <x-permission permission="create_roles">
                        <a href="{{ route('roles.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">New Role</span>
                        </a>
                    </x-permission>
                    <x-permission permission="view_users">
                        <a href="{{ route('users.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">User Permissions</span>
                        </a>
                    </x-permission>
                </div>
            </div>
        </x-permission>

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
                    <x-permission permission="access_dashboard">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Vadības panelis
                        </a>
                    </x-permission>
                    <x-permission permission="view_configs">
                        <a href="{{ route('configs.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-cog mr-2"></i>Configuration Settings
                        </a>
                    </x-permission>
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