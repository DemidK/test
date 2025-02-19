<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\NavLink;
use Barryvdh\DomPDF\Facade\Pdf;

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

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices',
            'invoice_date' => 'required|date',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_address' => 'required|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.vat' => 'required|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0'
        ]);
    
        // Calculate totals for each item and verify total amount
        $calculatedTotal = 0;
        foreach ($validated['items'] as &$item) {
            $subtotal = $item['quantity'] * $item['price'];
            $vatAmount = $item['vat'] > 0 ? ($subtotal * $item['vat'] / 100) : 0;
            $item['total'] = round($subtotal + $vatAmount, 2);
            $calculatedTotal += $item['total'];
        }
    
        // Verify the calculated total matches the submitted total
        if (abs(round($calculatedTotal, 2) - round($validated['total_amount'], 2)) > 0.01) {
            return back()->withErrors(['total_amount' => 'Total amount mismatch']);
        }
    
        $invoice = Invoice::create($validated);
    
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully');
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
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'invoice_date' => 'required|date',
            'customer_name' => 'required|string',
            'customer_email' => 'required|email',
            'customer_address' => 'required|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.vat' => 'required|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0'
        ]);

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
     * Export the specified invoice to PDF.
     */
    public function exportPdf(Invoice $invoice)
    {
        $calculations = $this->calculateInvoiceTotals($invoice);
        
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

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    /**
     * Calculate invoice totals, subtotals, and VAT.
     */
    private function calculateInvoiceTotals(Invoice $invoice): array
    {
        $subtotal = 0;
        $totalVat = 0;
        
        foreach ($invoice->items as $item) {
            $itemSubtotal = $item['quantity'] * $item['price'];
            $itemVat = $item['vat'] > 0 ? ($itemSubtotal * $item['vat'] / 100) : 0;
            
            $subtotal += $itemSubtotal;
            $totalVat += $itemVat;
        }

        return [
            'subtotal' => round($subtotal, 2),
            'total_vat' => round($totalVat, 2),
            'total' => round($subtotal + $totalVat, 2)
        ];
    }

    /**
     * Preview the PDF in browser
     */
    public function previewPdf(Invoice $invoice)
    {
        $calculations = $this->calculateInvoiceTotals($invoice);
        
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
}