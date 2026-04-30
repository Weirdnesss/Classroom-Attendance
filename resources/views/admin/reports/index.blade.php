@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-semibold">Reports</h1>
</div>

<div class="grid grid-cols-3 gap-6">

    {{-- Session Report --}}
    <div class="bg-white border border-gray-200 rounded p-6">
        <h2 class="font-semibold mb-1">Session Report</h2>
        <p class="text-sm text-gray-500 mb-4">Attendance for a single class session.</p>
        <a href="{{ route('admin.sessions.index') }}"
           class="block w-full text-center px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Browse Sessions
        </a>
    </div>

    {{-- Schedule Report --}}
    <div class="bg-white border border-gray-200 rounded p-6">
        <h2 class="font-semibold mb-1">Schedule Report</h2>
        <p class="text-sm text-gray-500 mb-4">Attendance summary across all sessions for a subject.</p>
        <a href="{{ route('admin.schedules.index') }}"
           class="block w-full text-center px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Browse Schedules
        </a>
    </div>

    {{-- Student Report --}}
    <div class="bg-white border border-gray-200 rounded p-6">
        <h2 class="font-semibold mb-1">Student Report</h2>
        <p class="text-sm text-gray-500 mb-4">Search for a student's full attendance record.</p>
        <a href="{{ route('admin.reports.student') }}"
            class="block w-full text-center px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Search Students
        </a>
    </div>

</div>
@endsection