@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Rediģēt rēķinu</h1>

        <x-invoice-form 
            :invoice="$item"
            :action="route('invoices.update', ['invoice' => $item])" 
            method="PUT"
        />
    </div>
</div>
@endsection