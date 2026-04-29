<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::withCount('devices')->latest()->get();
        return view('admin.rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('admin.rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'     => 'required|string|unique:rooms,code',
            'name'     => 'nullable|string',
            'building' => 'nullable|string',
            'floor'    => 'nullable|string',
            'capacity' => 'nullable|integer|min:1',
        ]);

        Room::create($request->only('code', 'name', 'building', 'floor', 'capacity'));

        return redirect()->route('admin.rooms.index')
                         ->with('success', 'Room created.');
    }

    public function edit(Room $room)
    {
        return view('admin.rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'code'      => 'required|string|unique:rooms,code,' . $room->id,
            'name'      => 'nullable|string',
            'building'  => 'nullable|string',
            'floor'     => 'nullable|string',
            'capacity'  => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $room->update($request->only('code', 'name', 'building', 'floor', 'capacity') + [
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.rooms.index')
                         ->with('success', 'Room updated.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('admin.rooms.index')
                         ->with('success', 'Room deleted.');
    }
}