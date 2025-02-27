<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\NavLink;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function index()
    {
        $configs = Config::all();
        $navLinks = NavLink::orderBy('position')->get();
        
        return view('config.index', [
            'configs' => $configs,
            'navLinks' => $navLinks
        ]);
    }

    public function edit($route)
    {
        $config = Config::where('route', $route)->firstOrFail();
        $navLinks = NavLink::orderBy('position')->get();

        return view('config.edit', [
            'config' => $config,
            'navLinks' => $navLinks
        ]);
    }

    public function update(Request $request, $route)
    {
        $config = Config::where('route', $route)->firstOrFail();
        $config->update([
            'data' => json_encode($request->data)
        ]);
        return redirect()->route('configs.index')->with('success', 'Configuration updated successfully');
    }
}