@extends('layouts.app')

@section('content')
<div class="text-center py-16">
    <h1 class="text-3xl font-bold mb-4">Paldies par reģistrāciju!</h1>
    <p class="text-lg mb-6">Jūsu personīgā platforma tiek sagatavota. Tas var aizņemt dažas minūtes.</p>
    <p class="mb-8">Kad viss būs gatavs, jūs varēsiet pieslēgties šeit:</p>
    @if(session('new_domain'))
        <a href="{{ session('new_domain') }}" class="text-xl font-bold text-blue-600 hover:underline">{{ session('new_domain') }}</a>
    @endif
    {{-- Можно добавить JavaScript, который будет периодически проверять доступность домена --}}
</div>
@endsection