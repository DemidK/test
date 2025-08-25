<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\NavLink;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $items = Config::all();
        $navLinks = NavLink::orderBy('position')->get();
        
        return view('config.index', [
            'items' => $items
        ]);
    }

    public function edit($key)
    {
        $config = Config::where('route', $key)->firstOrFail();
        $navLinks = NavLink::orderBy('position')->get();

        return view('config.edit', [
            'config' => $config
        ]);
    }

    public function update(Request $request, $key)
    {
        $config = Config::where('route', $key)->firstOrFail();
        $config->update([
            'data' => json_encode($request->data)
        ]);
        return redirect()->route('configs.index')->with('success', 'Configuration updated successfully');
    }
}