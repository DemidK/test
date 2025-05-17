<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    /**
     * Search for partners by name (AJAX endpoint)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchPartners(Request $request)
    {
        $query = $request->input('query');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $partners = Partner::where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->limit(10)
            ->get();
        
        $results = $partners->map(function ($partner) {
            // Base partner data
            $partnerData = [
                'id' => $partner->id,
                'name' => $partner->name,
                'identification_number' => $partner->identification_number,
                'vat' => '',
                'email' => '',
                'address' => '',
                'post_address' => ''
            ];
            
            // Extract contact information from json_data if available
            if (!empty($partner->json_data)) {
                // Look for contact info in any section of json_data
                foreach ($partner->json_data as $section => $fields) {
                    if (isset($fields['Email'])) {
                        $partnerData['email'] = $fields['Email'];
                    }
                    if (isset($fields['Address'])) {
                        $partnerData['address'] = $fields['Address'];
                    }
                    if (isset($fields['VAT Number'])) {
                        $partnerData['vat'] = $fields['VAT Number'];
                    }
                    if (isset($fields['Postal Address'])) {
                        $partnerData['post_address'] = $fields['Postal Address'];
                    }
                }
            }
            
            return $partnerData;
        });
        
        return response()->json($results);
    }

    protected function getPartnerDataConfig()
    {
        // Assuming you have a Config model
        $config = \App\Models\Config::where('route', 'partners_create')->first();
        
        if (!$config) {
            return ['default_inputs' => []]; // Default empty config
        }
        
        return is_string($config->data) ? json_decode($config->data, true) : $config->data;
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
        $dataObjectConfig = Config::getConfig('partners_create', []);
        return view("{$this->viewPath}.create", [
            'navLinks' => $this->navLinks(),
            'config' => $dataObjectConfig
        ]);
    }

    public function show($id)
    {
        $partner = $this->model::findOrFail($id);
        $formattedData = $this->formatDataForView($partner->json_data);
        $dataObjectConfig = Config::getConfig('partners_create', []);

        return view("{$this->viewPath}.show", [
            'navLinks' => $this->navLinks(),
            'partner' => $partner,
            'config' => $dataObjectConfig,
            'formattedData' => $formattedData
        ]);
    }

    public function edit($id)
    {
        $partner = $this->model::findOrFail($id);
        $formattedData = $this->formatDataForView($partner->json_data);
        $dataObjectConfig = Config::getConfig('partners_create', []);

        return view("{$this->viewPath}.edit", [
            'navLinks' => $this->navLinks(),
            'partner' => $partner,
            'config' => $dataObjectConfig,
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