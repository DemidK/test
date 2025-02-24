@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold">Klienta detaļas</h1>
            <div class="space-x-2">
                <a href="{{ route('partners.index') }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Partner Information Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Basic Info Section -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Vārds / Nosaukums</label>
                        <p class="text-gray-900">{{ $partner->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Identification Number</label>
                        <p class="text-gray-900">{{ $partner->identification_number }}</p>
                    </div>
                </div>

                <!-- Data Objects Section -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Data Objects</h2>
                    @if ($formattedData)
                        <div class="space-y-6">
                            @foreach ($formattedData as $dataObject)
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="font-semibold text-lg text-gray-900 mb-3">
                                        {{ $dataObject['object_name'] }}
                                    </h3>
                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach ($dataObject['items'] as $item)
                                            <div class="flex flex-col sm:flex-row">
                                                <span class="w-full sm:w-1/3 font-medium text-gray-600">
                                                    {{ $item['key'] }}:
                                                </span>
                                                <span class="w-full sm:w-2/3 text-gray-900">
                                                    {{ $item['value'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 italic">No data objects available.</p>
                    @endif
                </div>
            </div>

            <!-- Actions Section -->
            <div class="px-6 py-4 bg-gray-50 border-t">
                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('partners.edit', $partner) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-edit mr-2"></i>
                        Rediģēt klientu
                    </a>
                    <form action="{{ route('partners.destroy', $partner) }}" 
                          method="POST" 
                          class="inline-block"
                          onsubmit="return confirm('Are you sure you want to delete this partner?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center justify-center w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Partner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection