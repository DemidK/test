<!-- show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <!-- Navigation Bar -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <!-- Back Button -->
                <a href="{{ url()->previous() }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
                   title="Back">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Back</span>
                </a>

                <!-- To List Button -->
                <a href="{{ route('invoices.index') }}" 
                   class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
                   title="Invoices List">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>All Invoices</span>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <!-- Edit Button -->
                <a href="{{ route('invoices.edit', $item->id) }}" 
                   class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 inline-flex items-center gap-2"
                   title="Edit Invoice">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit</span>
                </a>

                <!-- Preview PDF -->
                <a href="{{ route('invoices.previewPdf', $item->id) }}" 
                   target="_blank"
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 inline-flex items-center gap-2"
                   title="Preview PDF">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Preview</span>
                </a>

                <!-- Download PDF -->
                <a href="{{ route('invoices.exportPdf', $item->id) }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 inline-flex items-center gap-2"
                   title="Download PDF">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Download</span>
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            {{-- Invoice Header --}}
            <div class="bg-gray-100 p-4">
                <div class="mb-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Invoice #{{ $item->invoice_number }}
                    </h1>
                    <p class="text-sm text-gray-600">
                        Issued on {{ \Carbon\Carbon::parse($item->invoice_date)->format('F d, Y') }}
                    </p>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="p-4 border-b">
                <x-customer-info :customer="$item" readonly="true" />
            </div>

            {{-- Invoice Items and Totals --}}
            <div class="p-4">
                <h3 class="font-semibold text-gray-700 mb-4">Invoice Items</h3>
                <x-invoice-items-table :items="$item->items" />
            </div>

            {{-- Additional Invoice Details --}}
            <div class="p-4 bg-white border-t text-sm text-gray-600">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <strong>Created By:</strong> 
                        {{ $item->updater ?? 'System' }}
                    </div>
                    <div class="text-right">
                        <strong>Invoice ID:</strong> 
                        {{ $item->id }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection