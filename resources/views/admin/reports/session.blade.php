@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-500 hover:underline">← Reports</a>
        <h1 class="text-xl font-semibold mt-1">
            {{ $session->classSchedule->subject->code }} — {{ $session->date->format('F d, Y') }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ $session->teacher->full_name }} &middot;
            {{ $session->room->code }} &middot;
            {{ $session->classSchedule->program->code }} Year {{ $session->classSchedule->year_level }}
        </p>
    </div>
    <a href="{{ route('admin.reports.session.export', $session) }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        Export CSV
    </a>
</div>

{{-- Summary --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    @php
        $present = $session->attendanceLogs->whereIn('status', ['present','late'])->count();
        $late    = $session->attendanceLogs->where('status', 'late')->count();
        $absent  = $session->attendanceLogs->where('status', 'absent')->count();
        $total   = $session->attendanceLogs->count();
        $rate    = $total > 0 ? round($present / $total * 100, 1) : 0;
    @endphp
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Present</p>
        <p class="text-2xl font-bold text-green-600 mt-1">{{ $present }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Late</p>
        <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $late }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Absent</p>
        <p class="text-2xl font-bold text-red-500 mt-1">{{ $absent }}</p>
    </div>
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Attendance Rate</p>
        <p class="text-2xl font-bold mt-1">{{ $rate }}%</p>
    </div>
</div>

{{-- Table --}}
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Student ID</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time In</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time Out</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Override</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($session->attendanceLogs->sortBy('student.last_name') as $log)
            <tr>
                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $log->student->student_id }}</td>
                <td class="px-4 py-3 font-medium">{{ $log->student->full_name }}</td>
                <td class="px-4 py-3">
                    @php $sc = ['present'=>'green','late'=>'yellow','absent'=>'red','excused'=>'blue']; @endphp
                    <span class="px-2 py-0.5 bg-{{ $sc[$log->status] }}-100 text-{{ $sc[$log->status] }}-700 rounded-full text-xs">
                        {{ ucfirst($log->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->time_in?->format('h:i A') ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $log->time_out?->format('h:i A') ?? '—' }}</td>
                <td class="px-4 py-3 text-xs text-gray-400">{{ $log->is_manual_override ? '* Manual' : '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection