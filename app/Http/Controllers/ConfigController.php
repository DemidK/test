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

    public function edit($key)
    {
        $config = Config::where('key', $key)->firstOrFail();
        $navLinks = NavLink::orderBy('position')->get();

        return view('config.edit', [
            'config' => $config,
            'navLinks' => $navLinks
        ]);
    }

    public function update(Request $request, $key)
    {
        $config = Config::where('key', $key)->firstOrFail();
        $config->update([
            'value' => json_encode($request->value)
        ]);

        return redirect()->route('configs.index')->with('success', 'Configuration updated successfully');
    }
}