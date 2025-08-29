<?php

namespace App\Http\Controllers;

use App\Models\DeviceInterface;
use Illuminate\Http\Request;

use App\Models\Device;

class DeviceInterfaceController extends Controller
{
    public function index()
    {
        $interfaces = DeviceInterface::with(['device', 'parentInterface'])->get();
        return view('device_interfaces.index', compact('interfaces'));
    }

    public function create()
    {
        $devices = Device::all();
        $parents = DeviceInterface::all();
        return view('device_interfaces.create', compact('devices', 'parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'device_interface_id' => 'nullable|exists:device_interfaces,id',
        ]);

        DeviceInterface::create($request->all());

        return redirect()->route('device_interfaces.index')->with('success', 'Interface created successfully.');
    }

    public function show(DeviceInterface $deviceInterface)
    {
        return view('device_interfaces.show', compact('deviceInterface'));
    }

    public function edit(DeviceInterface $deviceInterface)
    {
        $devices = Device::all();
        $parents = DeviceInterface::where('id', '!=', $deviceInterface->id)->get();
        return view('device_interfaces.edit', compact('deviceInterface', 'devices', 'parents'));
    }

    public function update(Request $request, DeviceInterface $deviceInterface)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'device_interface_id' => 'nullable|exists:device_interfaces,id',
        ]);

        $deviceInterface->update($request->all());

        return redirect()->route('device_interfaces.index')->with('success', 'Interface updated successfully.');
    }

    public function destroy(DeviceInterface $deviceInterface)
    {
        $deviceInterface->delete();
        return redirect()->route('device_interfaces.index')->with('success', 'Interface deleted successfully.');
    }
}