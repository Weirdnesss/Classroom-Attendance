@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.rooms.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Edit Room</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Code</label>
            <input type="text" name="code" value="{{ old('code', $room->code) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name', $room->name) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Building</label>
            <input type="text" name="building" value="{{ old('building', $room->building) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Floor</label>
            <input type="text" name="floor" value="{{ old('floor', $room->floor) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Capacity</label>
            <input type="number" name="capacity" value="{{ old('capacity', $room->capacity) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-6 flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
            <label for="is_active" class="text-sm">Active</label>
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Update
        </button>
    </form>
</div>
@endsection