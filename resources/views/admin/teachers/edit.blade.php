@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.teachers.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Edit Teacher</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}">
        @csrf @method('PUT')

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Personal Info</p>

        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name', $teacher->first_name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name', $teacher->last_name) }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $teacher->middle_name) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Employee ID</label>
            <input type="text" name="employee_id" value="{{ old('employee_id', $teacher->employee_id) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('employee_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Department</label>
            <select name="department_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Department —</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}"
                        {{ old('department_id', $teacher->department_id) == $dept->id ? 'selected' : '' }}>
                        {{ $dept->code }} — {{ $dept->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">RFID Tag</label>
            <input type="text" name="rfid_tag" value="{{ old('rfid_tag', $teacher->rfid_tag) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('rfid_tag') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6 flex items-center gap-2">
            <input type="checkbox" name="is_active" id="is_active" value="1"
                   {{ old('is_active', $teacher->is_active) ? 'checked' : '' }}>
            <label for="is_active" class="text-sm">Active</label>
        </div>

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Account</p>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email', $teacher->user->email) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">
                Password <span class="text-gray-400">(leave blank to keep current)</span>
            </label>
            <input type="password" name="password"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Update
        </button>
    </form>
</div>
@endsection