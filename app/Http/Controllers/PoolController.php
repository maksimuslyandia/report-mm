<?php

namespace App\Http\Controllers;

use App\Models\Pool;
use App\Models\Device;
use App\Models\InterfaceModel; // assuming your interface model
use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function index()
    {
        $pools = Pool::with(['device', 'interface'])->get();
        return view('pools.index', compact('pools'));
    }

    public function create()
    {
        $devices = Device::all();
        $interfaces = InterfaceModel::all();
        return view('pools.create', compact('devices', 'interfaces'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'interface_id' => 'required|exists:interfaces,id',
        ]);

        Pool::create($request->all());

        return redirect()->route('pools.index')->with('success', 'Pool created successfully.');
    }

    public function show(Pool $pool)
    {
        return view('pools.show', compact('pool'));
    }

    public function edit(Pool $pool)
    {
        $devices = Device::all();
        $interfaces = InterfaceModel::all();
        return view('pools.edit', compact('pool', 'devices', 'interfaces'));
    }

    public function update(Request $request, Pool $pool)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'interface_id' => 'required|exists:interfaces,id',
        ]);

        $pool->update($request->all());

        return redirect()->route('pools.index')->with('success', 'Pool updated successfully.');
    }

    public function destroy(Pool $pool)
    {
        $pool->delete();
        return redirect()->route('pools.index')->with('success', 'Pool deleted successfully.');
    }
}
