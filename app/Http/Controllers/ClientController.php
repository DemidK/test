<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends CrudController
{
    public function __construct()
    {
        $this->model = Client::class;
        $this->viewPath = 'clients';
        $this->routePrefix = 'clients';
        $this->searchableFields = ['name', 'identification_number'];
        $this->sortableFields = [
            'name' => 'name',
            'date' => 'created_at',
            'id' => 'identification_number'
        ];
        $this->defaultSort = ['created_at', 'desc'];
        $this->validationRules = [
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:255|unique:clients',
            'data' => 'nullable|array'
        ];
    }

    protected function getValidationRules($id = null): array
    {
        $rules = $this->validationRules;
        if ($id) {
            $rules['identification_number'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('clients')->ignore($id)
            ];
        }
        return $rules;
    }

    public function create()
    {
        $dataObjectConfig = Config::getConfig('client_data_objects', [
            'initial_count' => 1,
            'max_count' => 5,
            'background_color' => 'bg-gray-50',
            'default_items' => []
        ]);
        $navLinks = NavLink::orderBy('position')->get();
        
        return view("{$this->viewPath}.create", [
            'navLinks' => $navLinks,
            'config' => $dataObjectConfig
        ]);
    }

    public function show($id)
    {
        $client = $this->model::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        $formattedData = $this->formatDataForView($client->json_data);

        return view("{$this->viewPath}.show", [
            'navLinks' => $navLinks,
            'client' => $client,
            'formattedData' => $formattedData
        ]);
    }

    public function edit($id)
    {
        $client = $this->model::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        $formattedData = $this->formatDataForView($client->json_data);

        return view("{$this->viewPath}.edit", [
            'navLinks' => $navLinks,
            'client' => $client,
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