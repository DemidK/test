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
            @can('transportation_orders.index')
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Transportēšana
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('transportation_orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Visi pasūtījumi</span>
                        </a>
                        @can('transportation_orders.create')
                            <a href="{{ route('transportation_orders.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauns pasūtījums</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcan

            @can('partners.index')
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Partneri
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('partners.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Visi partneri</span>
                        </a>
                        @can('partners.create')
                            <a href="{{ route('partners.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauns partneris</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcan

            @can('invoices.index')
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Rēķini
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-40 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('invoices.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Visi rēķini</span>
                        </a>
                        @can('invoices.create')
                            <a href="{{ route('invoices.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Jauns rēķins</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcan

            @can('todos.index')
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        Задачи
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute hidden bg-white shadow-lg rounded-lg w-48 pt-2 group-hover:block" style="top: 100%; margin-top: -2px;">
                        <a href="{{ route('todos.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            <span class="text-base">Все задачи</span>
                        </a>
                        @can('todos.create')
                            <a href="{{ route('todos.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                                <span class="text-base">Новая задача</span>
                            </a>
                        @endcan
                    </div>
                </div>
            @endcan

            @auth
                <div class="relative group">
                    <button class="text-gray-700 hover:text-gray-900 px-3 py-2 focus:outline-none">
                        {{ Auth::user()->name }}
                        <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="absolute right-0 hidden bg-white shadow-lg rounded-lg w-56 pt-2 group-hover:block" style="top: 100%; margin-top: -2px; z-index: 50;">
                        @can('dashboard')
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Vadības panelis
                        </a>
                        @endcan

                        {{-- Iestatījumu bloks --}}
                        <div class="border-t border-gray-100 my-1"></div>
                        <div class="px-4 py-2 text-xs text-gray-400 uppercase">Pārvaldība</div>

                        @can('roles.index')
                        <a href="{{ route('roles.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Lomas un atļaujas
                        </a>
                        @endcan
                        @can('users.index')
                        <a href="{{ route('users.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Lietotāju pārvaldība
                        </a>
                        @endcan
                        @can('configs.index')
                        <a href="{{ route('configs.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                            Konfigurācija
                        </a>
                        @endcan

                        {{-- Кнопка обновления приложения --}}
                        @can('app.run_update')
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="{{ route('app.run_update') }}" class="block px-4 py-2 text-yellow-600 hover:bg-gray-100" onclick="return confirm('Вы уверены, что хотите запустить обновление приложения? Это может занять несколько минут.')">
                            <i class="fas fa-sync-alt mr-2"></i> Atjaunināt aplikāciju
                        </a>
                        @endcan

                        {{-- Izrakstīšanās --}}
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                                Izrakstīties
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login.schema') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Pieslēgties</a>
                <a href="{{ route('register.user') }}" class="text-gray-700 hover:text-gray-900 px-3 py-2">Reģistrēties</a>
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