@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.subjects.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Add Subject</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.subjects.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Department</label>
            <select name="department_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Department —</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->code }} — {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Code</label>
            <input type="text" name="code" value="{{ old('code') }}"
                   placeholder="e.g. CS201"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="e.g. Data Structures"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Units</label>
            <select name="units"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @foreach(range(1, 6) as $u)
                    <option value="{{ $u }}" {{ old('units') == $u ? 'selected' : '' }}>
                        {{ $u }} {{ $u == 1 ? 'unit' : 'units' }}
                    </option>
                @endforeach
            </select>
            @error('units') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Save
        </button>
    </form>
</div>
@endsection