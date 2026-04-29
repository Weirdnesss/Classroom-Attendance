@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.devices.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Add Device</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.devices.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Room</label>
            <select name="room_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Room —</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                        {{ $room->code }}{{ $room->name ? ' — ' . $room->name : '' }}
                    </option>
                @endforeach
            </select>
            @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Device UID</label>
            <input type="text" name="device_uid" value="{{ old('device_uid') }}"
                   placeholder="e.g. RFID-001"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('device_uid') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Name <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="e.g. Front Door Reader"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Save
        </button>
    </form>
</div>
@endsection