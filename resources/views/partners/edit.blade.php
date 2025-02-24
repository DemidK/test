<!-- edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Edit Partner</h1>
            <a href="{{ route('partners.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Back to Partners
            </a>
        </div>

        <x-partner-form 
            :partner="$partner"
            :action="route('partners.update', $partner->id)"
            method="PUT"
            :formatted-data="$formattedData"
        />
    </div>
</div>
@endsection
