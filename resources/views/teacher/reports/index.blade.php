@extends('layouts.teacher')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-semibold">Reports</h1>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Session Report --}}
    <div class="bg-white border border-gray-200 rounded p-6">
        <h2 class="font-semibold mb-1">Session Report</h2>
        <p class="text-sm text-gray-500 mb-4">Attendance for a single class session.</p>
        <a href="{{ route('teacher.sessions.index') }}"
           class="block w-full text-center px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Browse My Sessions
        </a>
    </div>

    {{-- Schedule Report --}}
    <div class="bg-white border border-gray-200 rounded p-6">
        <h2 class="font-semibold mb-1">Schedule Report</h2>
        <p class="text-sm text-gray-500 mb-4">Attendance summary across all sessions for a subject.</p>
        <div class="space-y-2">
            @forelse($schedules as $schedule)
            <a href="{{ route('teacher.reports.schedule', $schedule) }}"
               class="block px-3 py-2 border border-gray-200 rounded text-sm hover:bg-gray-50">
                <span class="font-medium">{{ $schedule->subject->code }}</span>
                <span class="text-gray-400 ml-2">{{ $schedule->program->code }} Year {{ $schedule->year_level }}</span>
            </a>
            @empty
            <p class="text-sm text-gray-400">No schedules assigned.</p>
            @endforelse
        </div>
    </div>

    {{-- Student Report --}}
    <div class="bg-white border border-gray-200 rounded p-6">
        <h2 class="font-semibold mb-1">Student Report</h2>
        <p class="text-sm text-gray-500 mb-4">Search for a student's attendance record.</p>
        <a href="{{ route('teacher.reports.student') }}"
           class="block w-full text-center px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Search Students
        </a>
    </div>

</div>
@endsection