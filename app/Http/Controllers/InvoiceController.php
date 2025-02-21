<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InvoiceController extends CrudController
{
    public function __construct()
    {
        $this->model = Invoice::class;
        $this->viewPath = 'invoices';
        $this->routePrefix = 'invoices';
        $this->searchableFields = ['invoice_number', 'customer_name', 'customer_email'];
        $this->sortableFields = [
            'date' => 'invoice_date',
            'amount' => 'total_amount',
            'number' => 'invoice_number'
        ];
        $this->defaultSort = ['created_at', 'desc'];
        $this->validationRules = [
            'invoice_number' => 'required|string|unique:invoices',
            'invoice_date' => 'required|date',
            'customer_id' => 'nullable|numeric',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_address' => 'required|string',
            'customer_vat' => 'nullable|string',
            'customer_post_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.vat' => 'required|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0',
            'total_vat' => 'required|numeric|min:0',
            'total_wo_vat' => 'required|numeric|min:0'
        ];
    }

    protected function getValidationRules($id = null): array
    {
        $rules = $this->validationRules;
        if ($id) {
            $rules['invoice_number'] = [
                'required',
                'string',
                Rule::unique('invoices')->ignore($id)
            ];
        }
        return $rules;
    }

    protected function storeItem(array $validated)
    {
        // Process items and calculate totals
        $processedData = $this->processInvoiceData($validated);
        $processedData['updater'] = auth()->user()->name ?? 'System';
        
        return $this->model::create($processedData);
    }

    protected function updateItem($item, array $validated)
    {
        $processedData = $this->processInvoiceData($validated);
        $processedData['updater'] = auth()->user()->name ?? 'System';
        
        return $item->update($processedData);
    }

    private function processInvoiceData(array $data): array
    {
        // Process items
        $processedItems = [];
        foreach ($data['items'] as $item) {
            $processedItems[] = [
                'description' => $item['description'],
                'quantity' => floatval($item['quantity']),
                'price' => floatval($item['price']),
                'vat' => floatval($item['vat']),
                'total' => round(
                    $item['quantity'] * $item['price'] * (1 + ($item['vat'] / 100)), 
                    2
                )
            ];
        }

        // Calculate totals
        $totals = $this->calculateInvoiceTotals($data['items']);
        
        return array_merge($data, [
            'items' => $processedItems,
            'total_wo_vat' => $totals['subtotal'],
            'total_vat' => $totals['total_vat'],
            'total_amount' => $totals['total']
        ]);
    }

    private function calculateInvoiceTotals(array $items): array
    {
        $subtotal = 0;
        $totalVat = 0;
        $grandTotal = 0;
        
        foreach ($items as $item) {
            $quantity = floatval($item['quantity']);
            $price = floatval($item['price']);
            $vatPercent = floatval($item['vat']);
            
            $itemSubtotal = $quantity * $price;
            $itemVat = $vatPercent > 0 ? ($itemSubtotal * $vatPercent / 100) : 0;
            $itemTotal = $itemSubtotal + $itemVat;
            
            $subtotal += $itemSubtotal;
            $totalVat += $itemVat;
            $grandTotal += $itemTotal;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'total_vat' => round($totalVat, 2),
            'total' => round($grandTotal, 2)
        ];
    }

    public function exportPdf($id)
    {
        $invoice = $this->model::findOrFail($id);
        $items = $invoice->items ?? [];
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'items'));
        
        $pdf->setPaper('a4');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function previewPdf($id)
    {
        $invoice = $this->model::findOrFail($id);
        $items = is_string($invoice->items) 
            ? json_decode($invoice->items, true) 
            : ($invoice->items ?? []);
        
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'items'));
        
        $pdf->setPaper('a4');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'defaultFont' => 'sans-serif'
        ]);

        return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
    }
}