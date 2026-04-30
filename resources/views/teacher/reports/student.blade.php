@extends('layouts.teacher')

@section('content')
<div class="mb-6">
    <a href="{{ route('teacher.reports.index') }}" class="text-sm text-gray-500 hover:underline">← Reports</a>
    <h1 class="text-xl font-semibold mt-1">Student Report</h1>
</div>

{{-- Search --}}
<div class="bg-white border border-gray-200 rounded p-4 mb-6 max-w-lg">
    <form method="GET" action="{{ route('teacher.reports.student') }}" class="flex gap-3">
        <input type="text" name="q" value="{{ $query }}"
               placeholder="Search by name or student ID..."
               autofocus
               class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-gray-400">
        <button type="submit"
                class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
            Search
        </button>
    </form>
</div>

{{-- Search Results List --}}
@if($query && !$student && $students->isNotEmpty())
<div class="bg-white border border-gray-200 rounded overflow-hidden mb-6 max-w-lg">
    <div class="px-4 py-2 bg-gray-50 border-b border-gray-200 text-xs text-gray-500">
        {{ $students->count() }} result(s) for "{{ $query }}"
    </div>
    @foreach($students as $s)
    <a href="{{ route('teacher.reports.student') }}?q={{ urlencode($query) }}&student_id={{ $s->id }}"
       class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
        <div>
            <p class="text-sm font-medium">{{ $s->full_name }}</p>
            <p class="text-xs text-gray-400">{{ $s->student_id }} &middot; {{ $s->program->code ?? '—' }} Year {{ $s->year_level }}</p>
        </div>
        <span class="text-xs text-blue-500">View Report →</span>
    </a>
    @endforeach
</div>
@endif

@if($query && !$student && $students->isEmpty())
<div class="bg-white border border-gray-200 rounded p-6 text-sm text-gray-400 max-w-lg">
    No students found for "{{ $query }}".
</div>
@endif

@if($student)
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-semibold">{{ $student->full_name }}</h2>
        <p class="text-sm text-gray-500">
            {{ $student->student_id }} &middot;
            {{ $student->program->code ?? '—' }} Year {{ $student->year_level }}
        </p>
    </div>
    <a href="{{ route('teacher.reports.student.export') }}?student_id={{ $student->id }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        Export CSV
    </a>
</div>

{{-- Summary --}}
<div class="grid grid-cols-5 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Present</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ $summary['present'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Late</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $summary['late'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Absent</p>
        <p class="text-2xl font-bold text-red-500 mt-1">{{ $summary['absent'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Excused</p>
        <p class="text-2xl font-bold text-blue-500 mt-1">{{ $summary['excused'] }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Attendance Rate</p>
        @php
            $rate = $summary['total'] > 0
                ? round(($summary['present'] + $summary['late']) / $summary['total'] * 100, 1)
                : 0;
        @endphp
        <p class="text-2xl font-bold mt-1 {{ $rate >= 75 ? 'text-green-600' : 'text-red-500' }}">
            {{ $rate }}%
        </p>
    </div>
</div>

{{-- Log --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Subject</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time In</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time Out</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($logs as $log)
            <tr>
                <td class="px-4 py-3 text-gray-500">{{ $log->classSession->date->format('M d, Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $log->classSession->classSchedule->subject->code }}</td>
                <td class="px-4 py-3">
                    @php $sc = ['present'=>'green','late'=>'yellow','absent'=>'red','excused'=>'blue']; @endphp
                    <span class="px-2 py-0.5 bg-{{ $sc[$log->status] }}-100 text-{{ $sc[$log->status] }}-700 rounded-full text-xs">
                        {{ ucfirst($log->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->time_in?->format('h:i A') ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->time_out?->format('h:i A') ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-6 text-center text-gray-400">No attendance records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif
@endsection