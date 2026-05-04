@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Add Student</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf

        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Personal Info</p>

        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">First Name</label>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Last Name</label>
                <input type="text" name="last_name" value="{{ old('last_name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Middle Name <span class="text-gray-400">(optional)</span></label>
            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Student ID</label>
            <input type="text" name="student_id" value="{{ old('student_id') }}"
                   placeholder="e.g. 2024-0001"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Program</label>
            <select name="program_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Program —</option>
                @foreach($programs as $program)
                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->code }} — {{ $program->name }}
                    </option>
                @endforeach
            </select>
            @error('program_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Year Level</label>
            <select name="year_level"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @foreach(range(1, 6) as $y)
                    <option value="{{ $y }}" {{ old('year_level') == $y ? 'selected' : '' }}>
                        Year {{ $y }}
                    </option>
                @endforeach
            </select>
            @error('year_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6 flex items-center gap-2">
            <input type="checkbox" name="is_irregular" id="is_irregular" value="1"
                   {{ old('is_irregular') ? 'checked' : '' }}>
            <label for="is_irregular" class="text-sm">Irregular student</label>
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Save
        </button>
    </form>
</div>
@endsection