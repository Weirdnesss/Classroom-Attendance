@extends('layouts.teacher')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-semibold">Welcome, {{ $teacher->full_name }}</h1>
    <p class="text-sm text-gray-500">{{ now()->format('l, F d, Y') }}</p>
</div>

{{-- Today's Sessions --}}
<h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Today's Sessions</h2>
@if($todaySessions->isEmpty())
    <div class="bg-white border border-gray-200 rounded p-6 text-sm text-gray-400 mb-6">
        No sessions today.
    </div>
@else
<div class="grid grid-cols-3 gap-4 mb-6">
    @foreach($todaySessions as $session)
    <a href="{{ route('teacher.sessions.show', $session) }}"
       class="bg-white border border-gray-200 rounded p-4 hover:border-gray-400 transition">
        <div class="flex items-center justify-between mb-2">
            <span class="font-medium text-sm">{{ $session->classSchedule->subject->code }}</span>
            @php $colors = ['pending'=>'gray','active'=>'green','completed'=>'blue','cancelled'=>'red']; @endphp
            <span class="px-2 py-0.5 bg-{{ $colors[$session->status] }}-100 text-{{ $colors[$session->status] }}-700 rounded-full text-xs">
                {{ ucfirst($session->status) }}
            </span>
        </div>
        <p class="text-xs text-gray-500">{{ $session->room->code }}</p>
        <div class="mt-3 flex gap-3 text-xs">
            <span class="text-green-600 font-medium">
                {{ $session->attendanceLogs->whereIn('status', ['present','late'])->count() }} Present
            </span>
            <span class="text-red-500 font-medium">
                {{ $session->attendanceLogs->where('status', 'absent')->count() }} Absent
            </span>
        </div>
        <div class="mt-2">
            @if($session->scan_mode === 'in')
                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">IN mode</span>
            @else
                <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs">OUT mode</span>
            @endif
        </div>
    </a>
    @endforeach
</div>
@endif

{{-- Today's Schedule --}}
<h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Today's Schedule</h2>
@if($todaySchedules->isEmpty())
    <div class="bg-white border border-gray-200 rounded p-6 text-sm text-gray-400 mb-6">
        No classes scheduled today.
    </div>
@else
<div class="bg-white border border-gray-200 rounded overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Subject</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Program / Year</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($todaySchedules as $schedule)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $schedule->subject->code }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $schedule->program->code }} Year {{ $schedule->year_level }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $schedule->room->code }}</td>
                <td class="px-4 py-3 text-gray-500">
                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} —
                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Upcoming Sessions --}}
@if($upcomingSessions->isNotEmpty())
<h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Upcoming This Week</h2>
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Subject</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($upcomingSessions as $session)
            <tr>
                <td class="px-4 py-3 text-gray-500">{{ $session->date->format('D, M d') }}</td>
                <td class="px-4 py-3 font-medium">{{ $session->classSchedule->subject->code }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $session->room->code }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection