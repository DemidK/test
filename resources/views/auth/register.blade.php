@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Reģistrēties</h2>

            {{--
               ИЗМЕНЕНО: Логика теперь использует флаг isTenantRegistration,
               а для регистрации пользователя берет $currentSchemaName,
               которая доступна глобально благодаря ViewServiceProvider.
            --}}
            @php
                $actionUrl = isset($isTenantRegistration) && $isTenantRegistration
                    ? route('register.tenant.submit')
                    : route('register.user.submit', ['schemaName' => $currentSchemaName]);
            @endphp

            <form method="POST" action="{{ $actionUrl }}">
                @csrf

                {{-- Поле для названия компании (URL) будет показано только на основном домене --}}
                @if(isset($isTenantRegistration) && $isTenantRegistration)
                    <div class="mb-4">
                        <label for="schema_name" class="block text-gray-700 text-sm font-bold mb-2">Jūsu kompānijas nosaukums (URL)</label>
                        <input type="text" name="schema_name" id="schema_name" value="{{ old('schema_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required placeholder="mana_kompanija">
                        <p class="text-gray-600 text-xs mt-1">Jūs varēsiet pieslēgties šeit: jūsu_nosaukums.{{ env('APP_DOMAIN', 'domain.lv') }}</p>
                        @error('schema_name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                {{-- Остальные поля остаются без изменений --}}
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Vārds / Nosaukums</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required autofocus>
                    @error('name')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-pasta adrese</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @error('email')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Parole</label>
                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    @error('password')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Apstiprināt paroli</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Reģistrēties
                    </button>
                    <a href="{{ route('login') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                        Pieslēgties
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection