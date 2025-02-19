<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\NavLink;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     */
    public function index()
    {
        $invoices = Invoice::latest()->get();
        $navLinks = NavLink::orderBy('position')->get();
        
        return view('invoices.index', [
            'navLinks' => $navLinks,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $navLinks = NavLink::orderBy('position')->get();
        
        return view('invoices.create', [
            'navLinks' => $navLinks,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Invoice Creation Request', [
            'input' => $request->all()
        ]);
    
        try {
            $validated = $request->validate([
                'invoice_number' => 'required|string|unique:invoices',
                'invoice_date' => 'required|date',
                'customer_name' => 'required|string',
                'customer_email' => 'required|email',
                'customer_address' => 'required|string',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.vat' => 'required|numeric|min:0|max:100',
                'total_amount' => 'required|numeric|min:0',
                'total_vat' => 'required|numeric|min:0',
                'total_wo_vat' => 'required|numeric|min:0'
            ]);
    
            // Ensure items are properly formatted
            $processedItems = [];
            foreach ($validated['items'] as $item) {
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
    
            // Recalculate totals
            $subtotal = 0;
            $totalVat = 0;
            $grandTotal = 0;
    
            foreach ($processedItems as $item) {
                $itemSubtotal = $item['quantity'] * $item['price'];
                $itemVat = $item['vat'] > 0 ? ($itemSubtotal * $item['vat'] / 100) : 0;
                $itemTotal = $itemSubtotal + $itemVat;
    
                $subtotal += $itemSubtotal;
                $totalVat += $itemVat;
                $grandTotal += $itemTotal;
            }
    
            // Override totals with calculated values
            $validated['items'] = $processedItems;
            $validated['total_wo_vat'] = round($subtotal, 2);
            $validated['total_vat'] = round($totalVat, 2);
            $validated['total_amount'] = round($grandTotal, 2);
    
            // Add updater information
            $validated['updater'] = auth()->user()->name ?? 'System';
    
            // Create the invoice
            $invoice = Invoice::create($validated);
    
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
    
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Invoice Creation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
    
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        $navLinks = NavLink::orderBy('position')->get();
        
        return view('invoices.show', [
            'navLinks' => $navLinks,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        $navLinks = NavLink::orderBy('position')->get();
        
        return view('invoices.edit', [
            'navLinks' => $navLinks,
            'invoice' => $invoice,
        ]);
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'invoice_number' => [
                'required', 
                'string', 
                Rule::unique('invoices')->ignore($invoice->id)
            ],
            'invoice_date' => 'required|date',
            'customer_id' => 'nullable|numeric',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_address' => 'required|string',
            'customer_vat' => 'nullable|string',
            'customer_post_address' => 'nullable|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.vat' => 'required|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0',
            'total_vat' => 'required|numeric|min:0',
            'total_wo_vat' => 'required|numeric|min:0'
        ]);

        // Calculate and verify totals
        $calculatedTotals = $this->calculateInvoiceTotals($validated['items']);
    
        // Verify the calculated totals match the submitted totals
        $this->validateInvoiceTotals($calculatedTotals, $validated);

        // Add updater information
        $validated['updater'] = auth()->user()->name ?? 'System';

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully');
    }

/**
 * Calculate invoice totals, subtotals, and VAT.
 *
 * @param array|null $items
 * @return array
 */
private function calculateInvoiceTotals(?array $items = []): array
{
    // Default values if no items
    $subtotal = 0;
    $totalVat = 0;
    $grandTotal = 0;
    
    // Only process if items is not null and is an array
    if (!empty($items)) {
        foreach ($items as $item) {
            // Ensure each item has required keys with default values
            $quantity = $item['quantity'] ?? 0;
            $price = $item['price'] ?? 0;
            $vatPercent = $item['vat'] ?? 0;
            
            $itemSubtotal = $quantity * $price;
            $itemVat = $vatPercent > 0 ? ($itemSubtotal * $vatPercent / 100) : 0;
            $itemTotal = $itemSubtotal + $itemVat;
            
            $subtotal += $itemSubtotal;
            $totalVat += $itemVat;
            $grandTotal += $itemTotal;
        }
    }

    return [
        'subtotal' => round($subtotal, 2),
        'total_vat' => round($totalVat, 2),
        'total' => round($grandTotal, 2)
    ];
}

public function exportPdf($id)
{
    // Use find() instead of model binding to get more control
    $invoice = Invoice::find($id);

    // Extensive debugging
    if (!$invoice) {
        Log::error('PDF Export - Invoice Not Found', [
            'invoice_id' => $id
        ]);
        
        return back()->with('error', 'Invoice not found');
    }

    // Ensure items are properly parsed
    $items = $invoice->items ?? [];
    
    $pdf = Pdf::loadView('invoices.pdf', [
        'invoice' => $invoice,
        'items' => $items
    ]);

    $pdf->setPaper('a4');
    $pdf->setOptions([
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'defaultFont' => 'sans-serif'
    ]);

    return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
}

/**
 * Preview the PDF in browser
 */
public function previewPdf(Invoice $invoice)
{
    // Ensure items is an array, fallback to empty array if null
    $items = $invoice->items ?? [];
    $calculations = $this->calculateInvoiceTotals($items);
    
    $pdf = Pdf::loadView('invoices.pdf', [
        'invoice' => $invoice,
        'calculations' => $calculations
    ]);

    $pdf->setPaper('a4');
    $pdf->setOptions([
        'isRemoteEnabled' => true,
        'isHtml5ParserEnabled' => true,
        'defaultFont' => 'sans-serif'
    ]);

    return $pdf->stream("invoice-{$invoice->invoice_number}.pdf");
}

    /**
     * Validate calculated totals against submitted totals.
     *
     * @param array $calculatedTotals
     * @param array $validated
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validateInvoiceTotals(array $calculatedTotals, array $validated)
    {
        $tolerance = 0.01; // Allow small floating-point discrepancies

        // Check total amount
        if (abs($calculatedTotals['total'] - $validated['total_amount']) > $tolerance) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_amount' => ['Total amount mismatch']
            ]);
        }

        // Check VAT amount
        if (abs($calculatedTotals['total_vat'] - $validated['total_vat']) > $tolerance) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_vat' => ['Total VAT amount mismatch']
            ]);
        }

        // Check subtotal
        if (abs($calculatedTotals['subtotal'] - $validated['total_wo_vat']) > $tolerance) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_wo_vat' => ['Total without VAT amount mismatch']
            ]);
        }
    }
}