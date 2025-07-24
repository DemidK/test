@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">
                {{-- Теперь проверяем простую переменную $schemaName --}}
                @if(isset($schemaName) && $schemaName)
                    Pieslēgties {{ $schemaName }}
                @else
                    Pieslēgties
                @endif
            </h2>
            
            {{-- Форма теперь тоже использует $schemaName для определения action --}}
            <form method="POST" action="{{ isset($schemaName) ? route('login.schema.submit', ['schemaName' => $schemaName]) : route('login') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">E-pasta adrese</label>
                    <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required autofocus>
                    @error('email')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Parole</label>
                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                    @error('password')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Pieslēgties
                    </button>
                    {{-- Ссылка на регистрацию теперь тоже проверяет $schemaName --}}
                    @if(!isset($schemaName))
                        <a href="{{ route('register.tenant') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                            Reģistrēties
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection