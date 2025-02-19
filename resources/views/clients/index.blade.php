@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8">Clients</h1>
    <a href="{{ route('clients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg mb-4">Add New Client</a>
    <div class="bg-white shadow-lg rounded-lg p-6">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="text-left">Name</th>
                    <th class="text-left">Surname</th>
                    <th class="text-left">Identification Number</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->surname }}</td>
                        <td>{{ $client->identification_number }}</td>
                        <td>
                            <a href="{{ route('clients.show', $client->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                            <a href="{{ route('clients.edit', $client->id) }}" class="text-green-600 hover:text-green-800 ml-2">Edit</a>
                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection