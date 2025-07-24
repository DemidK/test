<div class="flex items-center space-x-4">

    @if(isset($currentSchemaName))
        {{-- ========================================================== --}}
        {{-- ЭТА ЧАСТЬ БУДЕТ ОТОБРАЖАТЬСЯ ТОЛЬКО НА СУБДОМЕНАХ --}}
        {{-- ========================================================== --}}

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

        <div class="flex space-x-4">
            <x-permission permission="view_transportation_orders">
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Transportēšana
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('transportation_orders.index', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Visi pasūtījumi</span>
                        </a>
                        <x-permission permission="create_transportation_orders">
                            <a href="{{ route('transportation_orders.create', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauns pasūtījums</span>
                            </a>
                        </x-permission>
                    </div>
                </div>
            </x-permission>

            <x-permission permission="view_partners">
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Partneri
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('partners.index', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Visi partneri</span>
                        </a>
                        <x-permission permission="create_partners">
                            <a href="{{ route('partners.create', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauns partneris</span>
                            </a>
                        </x-permission>
                    </div>
                </div>
            </x-permission>

            <x-permission permission="view_invoices">
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Rēķini
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('invoices.index', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Visi rēķini</span>
                        </a>
                        <x-permission permission="create_invoices">
                            <a href="{{ route('invoices.create', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauns rēķins</span>
                            </a>
                        </x-permission>
                    </div>
                </div>
            </x-permission>
            
            <x-permission permission="view_permissions">
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Atļaujas
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-48 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('permissions.index', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Visas atļaujas</span>
                        </a>
                        <x-permission permission="create_permissions">
                            <a href="{{ route('permissions.create', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauna atļauja</span>
                            </a>
                        </x-permission>
                        <x-permission permission="view_roles">
                            <a href="{{ route('roles.index', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Pārvaldīt lomas</span>
                            </a>
                        </x-permission>
                        <x-permission permission="create_roles">
                            <a href="{{ route('roles.create', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauna loma</span>
                            </a>
                        </x-permission>
                        <x-permission permission="view_users">
                            <a href="{{ route('users.index', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Lietotāju atļaujas</span>
                            </a>
                        </x-permission>
                    </div>
                </div>
            </x-permission>

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
                            <a href="{{ route('dashboard', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Vadības panelis
                            </a>
                        </x-permission>
                        <x-permission permission="view_configs">
                            <a href="{{ route('configs.index', ['schemaName' => $currentSchemaName]) }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i>Konfigurācijas iestatījumi
                            </a>
                        </x-permission>
                        <form method="POST" action="{{ route('logout', ['schemaName' => $currentSchemaName]) }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Izrakstīties
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login.schema', ['schemaName' => $currentSchemaName]) }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Pieslēgties</a>
                <a href="{{ route('register.user', ['schemaName' => $currentSchemaName]) }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Reģistrēties</a>
            @endauth
        </div>

    @else
        {{-- ========================================================== --}}
        {{-- ЭТА ЧАСТЬ БУДЕТ ОТОБРАЖАТЬСЯ ТОЛЬКО НА ОСНОВНОМ ДОМЕНЕ --}}
        {{-- ========================================================== --}}
        <div class="flex items-center space-x-4 ml-auto">
            @guest
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Pieslēgties</a>
                <a href="{{ route('register.tenant') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Reģistrēties</a>
            @endguest
            @auth
                {{-- Ссылка на выход для администратора на основном домене --}}
                 <form method="POST" action="{{ route('logout.global') }}">
                    @csrf
                    <button type="submit" class="text-gray-700 hover:text-gray-900 px-3 py-2">Izrakstīties</button>
                 </form>
            @endauth
        </div>
    @endif
</div>