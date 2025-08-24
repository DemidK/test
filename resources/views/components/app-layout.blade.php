@extends('layouts.app')

{{-- Этот компонент-мост позволяет использовать синтаксис <x-app-layout> с вашим существующим макетом layouts.app --}}

@isset($header)
    @section('header')
        {{ $header }}
    @endsection
@endisset

@section('content')
    {{ $slot }}
@endsection