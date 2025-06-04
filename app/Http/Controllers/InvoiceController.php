<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Partner;
use App\Models\NavLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class InvoiceController extends CrudController
{
    public function __construct()
    {
        $this->model = Invoice::class;
        $this->viewPath = 'invoices';
        $this->routePrefix = 'invoices';
        $this->searchableFields = ['invoice_number', 'partner_name'];
        $this->sortableFields = [
            'date' => 'invoice_date',
            'amount' => 'total_amount',
            'number' => 'invoice_number'
        ];
        $this->defaultSort = ['created_at', 'desc'];
        $this->validationRules = [
            'invoice_number' => 'required|string|unique:invoices',
            'invoice_date' => 'nullable|date',
            'partner_id' => 'required|numeric',
            'partner_name' => 'required|string',
            'partner_email' => 'nullable|email',
            'partner_address' => 'nullable|string',
            'partner_vat' => 'nullable|string',
            'partner_post_address' => 'nullable|string',
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

    public function show($id)
    {
        $item = $this->model::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        
        if (is_string($item->items)) {
            $item->items = json_decode($item->items, true);
        }
        
        return view("{$this->viewPath}.show", [
            'navLinks' => $navLinks,
            'items' => $item]);
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
        $this->findOrCreatePartner($validated);
        
        // Process items and calculate totals
        $processedData = $this->processInvoiceData($validated);
        $processedData['updater'] = auth()->user()->name ?? 'System';
        
        return $this->model::create($processedData);
    }

    protected function updateItem($item, array $validated)
    {
        // If partner_id is not provided or not valid and we have partner details,
        // check if we need to create a new partner
        if ((empty($validated['partner_id']) || $validated['partner_id'] == 0) && !empty($validated['partner_name'])) {
            $partner = $this->findOrCreatePartner($validated);
            $validated['partner_id'] = $partner->id;
        }
        
        $processedData = $this->processInvoiceData($validated);
        $processedData['updater'] = auth()->user()->name ?? 'System';
        
        return $item->update($processedData);
    }

    /**
     * Find an existing partner by name or create a new one
     */
    private function findOrCreatePartner(array $data): Partner
    {
        // Try to find by exact name match first
        $partner = Partner::where('identification_number', $data['partner_id'])->first();
        
        if (!$partner) {
            // Generate a unique identification number if not provided
            $identificationNumber = !empty($data['partner_vat']) 
                ? $data['partner_vat'] 
                : 'CUST-' . date('YmdHis');
            
            // Create partner json_data structure
            $jsonData = [
                'Contact Information' => [
                    'Email' => $data['partner_email'] ?? '',
                    'Address' => $data['partner_address'] ?? '',
                    'Postal Address' => $data['partner_post_address'] ?? '',
                ]
            ];
            
            if (!empty($data['partner_vat'])) {
                $jsonData['Contact Information']['VAT Number'] = $data['partner_vat'];
            }
            
            try {
                // Create new partner
                $partner = Partner::create([
                    'name' => $data['partner_name'],
                    'identification_number' => $identificationNumber,
                    'json_data' => $jsonData
                ]);
                
                Log::info('Created new partner: ' . $data['partner_name'] . ' with ID: ' . $partner->id);
            } catch (\Exception $e) {
                Log::error('Failed to create partner: ' . $e->getMessage());
                throw $e;
            }
        }
        
        return $partner;
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
        
        // Use Snappy's PDF facade
        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'items'));
               
        // Snappy doesn't use setPaper or setOptions in the same way as DomPDF
        // You pass options directly to loadView or using specific methods.
        // For example, to set paper size or margins:
        $pdf->setPaper('a4')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10);
            // Add any other wkhtmltopdf options as needed.
            // Refer to wkhtmltopdf documentation for available options (e.g., --enable-local-file-access, etc.)

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function previewPdf($id)
    {
        $invoice = $this->model::findOrFail($id);
        $items = is_string($invoice->items) 
            ? json_decode($invoice->items, true) 
            : ($invoice->items ?? []);
        
        // Use Snappy's PDF facade
        $pdf = PDF::loadView('invoices.pdf', compact('invoice', 'items'));
        
        $pdf->setPaper('a4')
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('enable-local-file-access', true);

        return $pdf->inline("invoice-{$invoice->invoice_number}.pdf");
    }
}
