@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8">Client Details</h1>
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="mb-4">
            <label class="block text-gray-700">Name</label>
            <p>{{ $client->name }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Surname</label>
            <p>{{ $client->surname }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Identification Number</label>
            <p>{{ $client->identification_number }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Data Objects</label>
            @if ($formattedData)
                @foreach ($formattedData as $dataObject)
                    <div class="bg-gray-100 p-4 rounded-lg mb-4">
                        <h3 class="font-bold mb-2">{{ $dataObject['object_name'] }}</h3>
                        @foreach ($dataObject['items'] as $item)
                            <p>
                                <strong>{{ $item['key'] }}:</strong> 
                                    {{ $item['value'] }}
                            </p>
                        @endforeach
                    </div>
                @endforeach
            @else
                <p>No data objects available.</p>
            @endif
        </div>
        <a href="{{ route('clients.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Back to List</a>
    </div>
</div>
@endsection