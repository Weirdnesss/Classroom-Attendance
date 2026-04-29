@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Add Class Schedule</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-xl">
    <form method="POST" action="{{ route('admin.schedules.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Academic Term</label>
            <select name="academic_term_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Term —</option>
                @foreach($terms as $term)
                    <option value="{{ $term->id }}" {{ old('academic_term_id') == $term->id ? 'selected' : '' }}>
                        {{ $term->academicYear->label ?? '' }} — {{ $term->label }}
                    </option>
                @endforeach
            </select>
            @error('academic_term_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Subject</label>
            <select name="subject_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Subject —</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->code }} — {{ $subject->name }}
                    </option>
                @endforeach
            </select>
            @error('subject_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Teacher</label>
            <select name="teacher_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Teacher —</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->full_name }}
                    </option>
                @endforeach
            </select>
            @error('teacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

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

        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Program</label>
                <select name="program_id"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                    <option value="">— Select Program —</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->code }}
                        </option>
                    @endforeach
                </select>
                @error('program_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
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
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Days</label>
            <div class="flex gap-3 flex-wrap">
                @foreach(['Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <label class="flex items-center gap-1 text-sm">
                    <input type="checkbox" name="days[]" value="{{ $day }}"
                           {{ in_array($day, old('days', [])) ? 'checked' : '' }}>
                    {{ $day }}
                </label>
                @endforeach
            </div>
            @error('days') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6 grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Start Time</label>
                <input type="time" name="start_time" value="{{ old('start_time') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">End Time</label>
                <input type="time" name="end_time" value="{{ old('end_time') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Save & Auto-Enroll Students
        </button>
    </form>
</div>
@endsection