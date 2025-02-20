<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class CrudController extends Controller
{
    protected $model;

    public function index()
    {
        $items = $this->model->all();
        return view('crud.index', compact('items'));
    }

    public function create()
    {
        return view('crud.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $this->beforeCreate($data);
        $item = $this->model->create($data);
        $this->afterCreate($item);
        return redirect()->route('crud.index');
    }

    public function show($id)
    {
        $item = $this->model->findOrFail($id);
        return view('crud.show', compact('item'));
    }

    public function edit($id)
    {
        $item = $this->model->findOrFail($id);
        return view('crud.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = $this->model->findOrFail($id);
        $data = $request->all();
        $this->beforeUpdate($item, $data);
        $item->update($data);
        $this->afterUpdate($item);
        return redirect()->route('crud.index');
    }

    public function destroy($id)
    {
        $this->model->destroy($id);
        return redirect()->route('crud.index');
    }

    protected function beforeCreate(&$data)
    {
        // Perform any actions before creating the item
        // Modify $data if needed
    }

    protected function afterCreate($item)
    {
        // Perform any actions after creating the item
    }

    protected function beforeUpdate($item, &$data)
    {
        // Perform any actions before updating the item
        // Modify $item or $data if needed
    }

    protected function afterUpdate($item)
    {
        // Perform any actions after updating the item
    }
}