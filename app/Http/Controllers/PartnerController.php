<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerController extends CrudController
{
    public function __construct()
    {
        $this->model = Partner::class;
        $this->viewPath = 'partners';
        $this->routePrefix = 'partners';
        $this->searchableFields = ['name', 'identification_number'];
        $this->sortableFields = [
            'name' => 'name',
            'date' => 'created_at',
            'id' => 'identification_number'
        ];
        $this->defaultSort = ['created_at', 'desc'];
        $this->validationRules = [
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:255|unique:partners',
            'data' => 'nullable|array'
        ];
    }

    protected function getPartnerDataConfig()
    {
        // Assuming you have a Config model
        $config = \App\Models\Config::where('key', 'partner_data_objects')->first();
        
        if (!$config) {
            return ['partner_create' => []]; // Default empty config
        }
        
        return is_string($config->value) ? json_decode($config->value, true) : $config->value;
    }

    protected function getValidationRules($id = null): array
    {
        $rules = $this->validationRules;
        if ($id) {
            $rules['identification_number'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('partners')->ignore($id)
            ];
        }
        return $rules;
    }

    public function create()
    {
        $dataObjectConfig = Config::getConfig('partner_data_objects', [
            'initial_count' => 1,
            'max_count' => 5,
            'background_color' => 'bg-gray-50',
            'default_items' => []
        ]);
        
        return view("{$this->viewPath}.create", [
            'navLinks' => $this->navLinks(),
            'config' => $dataObjectConfig
        ]);
    }

    public function show($id)
    {
        $partner = $this->model::findOrFail($id);
        $formattedData = $this->formatDataForView($partner->json_data);

        return view("{$this->viewPath}.show", [
            'navLinks' => $this->navLinks(),
            'partner' => $partner,
            'formattedData' => $formattedData
        ]);
    }

    public function edit($id)
    {
        $partner = $this->model::findOrFail($id);
        $formattedData = $this->formatDataForView($partner->json_data);

        return view("{$this->viewPath}.edit", [
            'navLinks' => $this->navLinks(),
            'partner' => $partner,
            'formattedData' => $formattedData
        ]);
    }

    protected function storeItem(array $validated)
    {
        $formattedData = $this->formatJsonData($validated['data'] ?? []);
        
        return $this->model::create([
            'name' => $validated['name'],
            'identification_number' => $validated['identification_number'],
            'json_data' => $formattedData,
        ]);
    }

    protected function updateItem($item, array $validated)
    {
        $formattedData = $this->formatJsonData($validated['data'] ?? []);
        
        return $item->update([
            'name' => $validated['name'],
            'identification_number' => $validated['identification_number'],
            'json_data' => $formattedData,
        ]);
    }

    private function formatJsonData(array $data): array
    {
        $formattedData = [];
        if (!empty($data)) {
            foreach ($data as $object) {
                $objectName = $object['object_name'];
                $formattedData[$objectName] = [];
                
                foreach ($object['items'] as $item) {
                    $formattedData[$objectName][$item['key']] = $item['value'];
                }
            }
        }
        return $formattedData;
    }

    private function formatDataForView(?array $jsonData): array
    {
        $formattedData = [];
        if ($jsonData) {
            foreach ($jsonData as $object_name => $items) {
                $dataObject = [
                    'object_name' => $object_name,
                    'items' => []
                ];
                
                foreach ($items as $key => $value) {
                    $dataObject['items'][] = [
                        'key' => $key,
                        'value' => $value
                    ];
                }
                
                $formattedData[] = $dataObject;
            }
        }
        return $formattedData;
    }
}