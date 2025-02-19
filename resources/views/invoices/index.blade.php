@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-3xl font-bold mb-8">Invoices</h1>
    <a href="{{ route('invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg mb-4">Create New Invoice</a>
    <div class="bg-white shadow-lg rounded-lg p-6">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="text-left">Invoice Number</th>
                    <th class="text-left">Customer Name</th>
                    <th class="text-left">Total Amount</th>
                    <th class="text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)

                    <tr>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>${{ number_format($invoice->total_amount, 2) }}</td>
                        <td>
                            <a href="{{ route('invoices.show', $invoice->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                            <a href="{{ route('invoices.exportPdf', $invoice->id) }}" class="text-green-600 hover:text-green-800 ml-2">Export PDF</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection