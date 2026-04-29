@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.sessions.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">Create Manual Session</h1>
</div>

<div class="bg-white border border-gray-200 rounded p-6 max-w-lg">
    <form method="POST" action="{{ route('admin.sessions.store') }}">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Class Schedule</label>
            <select name="class_schedule_id"
                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
                <option value="">— Select Schedule —</option>
                @foreach($schedules as $schedule)
                    <option value="{{ $schedule->id }}" {{ old('class_schedule_id') == $schedule->id ? 'selected' : '' }}>
                        {{ $schedule->subject->code }} — {{ $schedule->program->code }}
                        Year {{ $schedule->year_level }} — {{ $schedule->teacher->full_name }}
                    </option>
                @endforeach
            </select>
            @error('class_schedule_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-1">Date</label>
            <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
            @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Create Session
        </button>
    </form>
</div>
@endsection