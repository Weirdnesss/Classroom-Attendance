<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Room;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::with('room')->latest()->get();
        return view('admin.devices.index', compact('devices'));
    }

    public function create()
    {
        $rooms = Room::where('is_active', true)->orderBy('code')->get();
        return view('admin.devices.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'device_uid' => 'required|string|unique:devices,device_uid',
            'name'       => 'nullable|string',
        ]);

        Device::create($request->only('room_id', 'device_uid', 'name'));

        return redirect()->route('admin.devices.index')
                         ->with('success', 'Device created.');
    }

    public function edit(Device $device)
    {
        $rooms = Room::where('is_active', true)->orderBy('code')->get();
        return view('admin.devices.edit', compact('device', 'rooms'));
    }

    public function update(Request $request, Device $device)
    {
        $request->validate([
            'room_id'    => 'required|exists:rooms,id',
            'device_uid' => 'required|string|unique:devices,device_uid,' . $device->id,
            'name'       => 'nullable|string',
            'is_active'  => 'boolean',
        ]);

        $device->update($request->only('room_id', 'device_uid', 'name') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.devices.index')
                         ->with('success', 'Device updated.');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return redirect()->route('admin.devices.index')
                         ->with('success', 'Device deleted.');
    }
}