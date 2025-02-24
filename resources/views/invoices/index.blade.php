@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl md:max-w-3xl lg:max-w-5xl xl:max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Invoices</h1>
            <a href="{{ route('invoices.create') }}" 
               class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Izveidot rēķinu
            </a>
        </div>

        <!-- Filters Section -->
        <form action="{{ route('invoices.index') }}" method="GET" class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search invoices..." 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <select name="sort_by" 
                            class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            onchange="this.form.submit()">
                        <option value="date" {{ request('sort_by') === 'date' ? 'selected' : '' }}>Date</option>
                        <option value="amount" {{ request('sort_by') === 'amount' ? 'selected' : '' }}>Amount</option>
                        <option value="number" {{ request('sort_by') === 'number' ? 'selected' : '' }}>Invoice #</option>
                    </select>
                    <select name="sort_order" 
                            class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            onchange="this.form.submit()">
                        <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Search
                    </button>
                    @if(request()->hasAny(['search', 'sort_by', 'sort_order']))
                        <a href="{{ route('invoices.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Invoices List -->
        <div class="bg-white rounded-lg shadow-md">
            <!-- Mobile View -->
            <div class="sm:hidden">
                @foreach($items as $invoice)
                <div class="p-4 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:text-blue-900">
                                <div class="font-medium">#{{ $invoice->invoice_number }}</div>
                                <div class="text-sm">{{ $invoice->partner_name }}</div>
                            </a>
                            <div class="text-sm text-gray-900 mt-1">${{ number_format($invoice->total_amount, 2) }}</div>
                            <div class="mt-2">
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('invoices.previewPdf', $invoice->id) }}" 
                               target="_blank" 
                               class="text-green-600 hover:text-green-900"
                               title="Priekšskatīt PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            
                            <a href="{{ route('invoices.exportPdf', $invoice->id) }}" 
                               class="text-purple-600 hover:text-purple-900"
                               title="Lejupielādēt PDF">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </a>
                            
                            <a href="{{ route('invoices.edit', $invoice->id) }}" 
                               class="text-yellow-600 hover:text-yellow-900"
                               title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            
                            <form action="{{ route('invoices.destroy', $invoice) }}"
                            method="POST" 
                                class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('Are you sure?')"
                                        title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Desktop View -->
            <div class="hidden sm:block">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($items as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <a href="{{ route('invoices.show', $invoice->id) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        #{{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td class="px-4 py-4">{{ $invoice->partner_name }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    {{ $invoice->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    ${{ number_format($invoice->total_amount, 2) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $invoice->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-3">
                                        @include('components.invoice-actions', ['invoice' => $invoice])
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($items, 'links'))
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection