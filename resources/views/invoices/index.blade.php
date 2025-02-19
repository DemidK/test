@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold mb-4 sm:mb-0">Invoices</h1>
            <a href="{{ route('invoices.create') }}" 
               class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Create Invoice
            </a>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           placeholder="Search invoices..." 
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <select class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                    </select>
                    <select class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Sort by</option>
                        <option value="date">Date</option>
                        <option value="amount">Amount</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="bg-white rounded-lg shadow-md">
            <!-- Mobile View -->
            <div class="sm:hidden">
                @foreach($invoices as $invoice)
                    <div class="p-4 border-b">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900">#{{ $invoice->invoice_number }}</div>
                                <div class="text-sm text-gray-500">{{ $invoice->customer_name }}</div>
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
                            <div class="flex gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('invoices.edit', $invoice) }}" 
                                   class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('invoices.destroy', $invoice) }}" 
                                      method="POST" 
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800 p-2"
                                            onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
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
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    #{{ $invoice->invoice_number }}
                                </td>
                                <td class="px-4 py-4">
                                    {{ $invoice->customer_name }}
                                </td>
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
                                <td class="px-4 py-4 text-right space-x-2">
                                    <a href="{{ route('invoices.show', $invoice) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', $invoice) }}" 
                                       class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('invoices.destroy', $invoice) }}" 
                                          method="POST" 
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800"
                                                onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($invoices, 'links'))
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection