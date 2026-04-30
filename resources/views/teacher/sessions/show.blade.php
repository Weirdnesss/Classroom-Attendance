@extends('layouts.teacher')

@section('content')
<div class="mb-6">
    <a href="{{ route('teacher.sessions.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <a href="{{ route('teacher.reports.session', $session) }}"
        class="text-sm text-blue-500 hover:underline ml-4">View Report</a>
    <h1 class="text-xl font-semibold mt-1">
        {{ $session->classSchedule->subject->code }} — {{ $session->date->format('M d, Y') }}
    </h1>
    <p class="text-sm text-gray-500 mt-1">
        {{ $session->room->code }} &middot;
        {{ $session->classSchedule->program->code }} Year {{ $session->classSchedule->year_level }}
    </p>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    {{-- Status --}}
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Status</p>
        <form method="POST" action="{{ route('teacher.sessions.updateStatus', $session) }}">
            @csrf
            <select name="status" onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                @foreach(['pending','active','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ $session->status === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Scan Mode --}}
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Scan Mode</p>
        <div class="flex items-center justify-between">
            <span class="text-lg font-bold {{ $session->scan_mode === 'in' ? 'text-blue-600' : 'text-purple-600' }}">
                {{ strtoupper($session->scan_mode) }}
            </span>
            <form method="POST" action="{{ route('teacher.sessions.toggleScanMode', $session) }}">
                @csrf
                <button class="px-3 py-1 bg-gray-900 text-white text-xs rounded hover:bg-gray-700">
                    Toggle
                </button>
            </form>
        </div>
    </div>

    {{-- Summary --}}
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Attendance</p>
        <div class="flex gap-3 text-sm">
            <span class="text-green-600 font-medium">
                {{ $session->attendanceLogs->whereIn('status', ['present','late'])->count() }} Present
            </span>
            <span class="text-red-500 font-medium">
                {{ $session->attendanceLogs->where('status', 'absent')->count() }} Absent
            </span>
            <span class="text-yellow-600 font-medium">
                {{ $session->attendanceLogs->where('status', 'late')->count() }} Late
            </span>
        </div>
    </div>
</div>

{{-- Attendance Table --}}
<h2 class="text-sm font-semibold mb-3">Student Attendance</h2>
<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Student</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">ID</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time In</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time Out</th>
                <th class="px-4 py-3">Override</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($session->attendanceLogs as $log)
            <tr>
                <td class="px-4 py-3">{{ $log->student->full_name }}</td>
                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $log->student->student_id }}</td>
                <td class="px-4 py-3">
                    @php $sc = ['present'=>'green','late'=>'yellow','absent'=>'red','excused'=>'blue']; @endphp
                    <span class="px-2 py-0.5 bg-{{ $sc[$log->status] }}-100 text-{{ $sc[$log->status] }}-700 rounded-full text-xs">
                        {{ ucfirst($log->status) }}
                    </span>
                    @if($log->is_manual_override)
                        <span class="text-xs text-gray-400 ml-1">*</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $log->time_in ? $log->time_in->format('h:i A') : '—' }}
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $log->time_out ? $log->time_out->format('h:i A') : '—' }}
                </td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('teacher.sessions.override', $session) }}">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $log->student_id }}">
                        <select name="status" onchange="this.form.submit()"
                                class="text-xs border border-gray-300 rounded px-1 py-0.5">
                            @foreach(['present','late','absent','excused'] as $s)
                                <option value="{{ $s }}" {{ $log->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection