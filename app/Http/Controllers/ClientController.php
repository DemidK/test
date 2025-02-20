<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Config;
use App\Models\NavLink;
use Illuminate\Support\Facades\Log as FacadesLog;

class ClientController extends Controller
{
    /**
     * Display a listing of the clients.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $clients = Client::paginate(10);
        $navLinks = NavLink::orderBy('position')->get();
        // Example data to pass to the view
        $data = [
            'navLinks' => $navLinks,
            'clients' => $clients,
        ];
        return view('clients.index', $data);
    }

    /**
     * Show the form for creating a new client.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $dataObjectConfig = Config::getConfig('client_data_objects', [
            'initial_count' => 1,
            'max_count' => 5,
            'background_color' => 'bg-gray-50',
            'default_items' => []
        ]);
        $navLinks = NavLink::orderBy('position')->get();
        // Example data to pass to the view
        $data = [
            'navLinks' => $navLinks,
            'config' => $dataObjectConfig
        ];
        
        return view('clients.create', $data);
    }

    /**
     * Store a newly created client in the database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:255|unique:clients',
            'data' => 'nullable|array'
        ]);
    
        $formattedData = [];
        if ($request->input('data')) {
            foreach ($request->input('data') as $object) {             
                foreach ($object['items'] as $item) {
                    $formattedData[$object['object_name']][$item['key']] = $item['value'];
                }
            }
        }

        Client::create([
            'name' => $request->input('name'),
            'identification_number' => $request->input('identification_number'),
            'json_data' => $formattedData,
        ]);
    
        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show($id)
    {
        $client = Client::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        
        $formattedData = [];
        if ($client->json_data) {
            foreach ($client->json_data as $object_name => $items) {
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

        $data = [
            'navLinks' => $navLinks,
            'client' => $client,
            'formattedData' => $formattedData
        ];
        return view('clients.show', $data);
    }
   
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $navLinks = NavLink::orderBy('position')->get();
        $formattedData = [];

        if ($client->json_data) {
            foreach ($client->json_data as $object_name => $data) {
                $dataObject = [
                    'object_name' => $object_name,
                    'items' => []
                ];
                
                foreach ($data as $key => $value) {
                    $dataObject['items'][] = [
                        'key' => $key,
                        'value' => $value
                    ];
                }
                
                $formattedData[] = $dataObject;
            }
        }
    
        $data = [
            'navLinks' => $navLinks,
            'client' => $client,
            'formattedData' => $formattedData
        ];
        return view('clients.edit', $data);
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:255|unique:clients,identification_number,' . $id,
            'data' => 'nullable|array'
        ]);
    
        // Restructure data to match JSON storage format
        $jsonData = [];
        if ($request->input('data')) {
            foreach ($request->input('data') as $dataObject) {
                $objectName = $dataObject['object_name'];
                $jsonData[$objectName] = [];
                
                foreach ($dataObject['items'] as $item) {
                    $jsonData[$objectName][$item['key']] = $item['value'];
                }
            }
        }
    
        $client = Client::findOrFail($id);
        $client->update([
            'name' => $request->input('name'),
            'identification_number' => $request->input('identification_number'),
            'json_data' => $jsonData,
        ]);
    
        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified client from the database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}