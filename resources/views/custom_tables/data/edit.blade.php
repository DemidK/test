<!-- resources/views/custom_tables/data/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-2">
                <a href="{{ route('custom-tables.data.index', $customTable->name) }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left"></i> Back to {{ $customTable->display_name }}
                </a>
                <span class="text-gray-400">/</span>
                <h1 class="text-2xl font-bold">Edit Record #{{ $item->id }}</h1>
            </div>
            @if($customTable->description)
                <p class="text-gray-600">{{ $customTable->description }}</p>
            @endif
        </div>

        <x-custom-table.data-form 
            :customTable="$customTable"
            :item="$item"
            :action="route('custom-tables.data.update', [$customTable->name, $item->id])"
        />
    </div>
</div>
@endsection