<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientDevice;
use Illuminate\Http\Request;

class ClientDeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $status = request()->query('status');
        
        $devices = ClientDevice::with(['client', 'product', 'order'])
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.devices.index', compact('devices'));
    }

    public function show(ClientDevice $device)
    {
        $device->load(['client', 'product', 'order']);
        return view('admin.devices.show', compact('device'));
    }

    public function edit(ClientDevice $device)
    {
        $device->load(['client', 'product', 'order']);
        return view('admin.devices.edit', compact('device'));
    }

    public function update(Request $request, ClientDevice $device)
    {
        $request->validate([
            'mqtt_topic' => 'required|string|unique:client_devices,mqtt_topic,' . $device->id,
            'device_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        $device->update([
            'mqtt_topic' => '/project/startup/client/' . $request->mqtt_topic,
            'device_name' => $request->device_name,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device updated successfully.');
    }

    public function destroy(ClientDevice $device)
    {
        $device->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device deleted successfully.');
    }
}