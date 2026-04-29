@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.rooms.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Add Room</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.rooms.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Code</label>
            <input type="text" name="code" value="{{ old('code') }}"
                   placeholder="e.g. RM301"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Name <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="e.g. Computer Lab 1"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Building <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="building" value="{{ old('building') }}"
                   placeholder="e.g. Main Building"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Floor <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="floor" value="{{ old('floor') }}"
                   placeholder="e.g. 3rd Floor"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Capacity <span class="text-gray-400">(optional)</span></label>
            <input type="number" name="capacity" value="{{ old('capacity') }}"
                   placeholder="e.g. 40"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Save
        </button>
    </form>
</div>
@endsection