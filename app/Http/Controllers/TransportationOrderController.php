<?php

namespace App\Http\Controllers;

use App\Models\TransportationOrder;
use App\Models\Partner;
use App\Services\TransportationOrderPdfService;
use Illuminate\Http\Request;

class TransportationOrderController extends CrudController
{
    public function __construct()
    {
        $this->model = TransportationOrder::class;
        $this->viewPath = 'transportation_orders';
        $this->routePrefix = 'transportation_orders';
        $this->searchableFields = ['carrier_name', 'order_number', 'cargo_type'];
        $this->sortableFields = [
            'name' => 'carrier_name',
            'date' => 'created_at',
            'cargo' => 'cargo_type'
        ];
        $this->defaultSort = ['created_at', 'desc'];
        $this->validationRules = [
            'carrier_name' => 'required|string|max:255',
            'reg_number' => 'nullable|string|max:255',
            'cargo_type' => 'nullable|string|max:255',
            'partner_id' => 'nullable|exists:partners,id'
        ];
    }

    public function create()
    {
        $partners = Partner::orderBy('name')->get();
        $cargoTypes = $this->getCargoTypes();
        
        return view("{$this->viewPath}.create", [
            'partners' => $partners,
            'cargoTypes' => $cargoTypes
        ]);
    }

    public function edit($id)
    {
        $order = $this->model::findOrFail($id);
        $partners = Partner::orderBy('name')->get();
        $cargoTypes = $this->getCargoTypes();
        
        return view("{$this->viewPath}.edit", [
            'order' => $order,
            'partners' => $partners,
            'cargoTypes' => $cargoTypes
        ]);
    }

    public function exportPdf(TransportationOrderPdfService $pdfService, $id)
    {
        $order = $this->model::findOrFail($id);
        return $pdfService->generate($order);
    }

    private function getCargoTypes()
    {
        return [
            'Автомобили', 'Алкогольные напитки', 'Безалк. напитки', 'Бумага', 
            'Бытовая техника', 'Грибы', 'Древесина', 'Другое', 
            // ... Add all the cargo types from the original form
        ];
    }

    public function show($id)
    {
        $order = $this->model::findOrFail($id);
        
        return view("{$this->viewPath}.show", [
            'order' => $order
        ]);
    }
}