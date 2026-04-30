@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.sessions.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <a href="{{ route('admin.reports.session', $session) }}"
        class="text-sm text-blue-500 hover:underline ml-4">View Report</a>
    <h1 class="text-xl font-semibold mt-1">
        {{ $session->classSchedule->subject->code }} — {{ $session->date->format('M d, Y') }}
    </h1>
    <p class="text-sm text-gray-500 mt-1">
        {{ $session->teacher->full_name }} &middot;
        {{ $session->room->code }} &middot;
        {{ $session->classSchedule->program->code }} Year {{ $session->classSchedule->year_level }}
    </p>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    {{-- Status --}}
    <div class="bg-white border border-gray-200 rounded p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Status</p>
        <form method="POST" action="{{ route('admin.sessions.updateStatus', $session) }}">
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
            <form method="POST" action="{{ route('admin.sessions.toggleScanMode', $session) }}">
                @csrf
                <button class="px-3 py-1 bg-gray-900 text-white text-xs rounded hover:bg-gray-700">
                    Toggle
                </button>
            </form>
        </div>
    </div>

    {{-- Attendance Summary --}}
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

<div class="grid grid-cols-2 gap-6">

    {{-- Attendance Logs --}}
    <div>
        <h2 class="text-sm font-semibold mb-3">Student Attendance</h2>
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Student</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Status</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Time In</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Time Out</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($session->attendanceLogs as $log)
                    <tr>
                        <td class="px-4 py-2">{{ $log->student->full_name }}</td>
                        <td class="px-4 py-2">
                            @php
                                $sc = ['present'=>'green','late'=>'yellow','absent'=>'red','excused'=>'blue'];
                            @endphp
                            <span class="px-2 py-0.5 bg-{{ $sc[$log->status] }}-100 text-{{ $sc[$log->status] }}-700 rounded-full text-xs">
                                {{ ucfirst($log->status) }}
                            </span>
                            @if($log->is_manual_override)
                                <span class="text-xs text-gray-400 ml-1">*</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-gray-500 text-xs">
                            {{ $log->time_in ? $log->time_in->format('h:i A') : '—' }}
                        </td>
                        <td class="px-4 py-2 text-gray-500 text-xs">
                            {{ $log->time_out ? $log->time_out->format('h:i A') : '—' }}
                        </td>
                        <td class="px-4 py-2 text-right">
                            <form method="POST" action="{{ route('admin.sessions.override', $session) }}">
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
    </div>

    {{-- Session Period Rules --}}
    <div>
        <h2 class="text-sm font-semibold mb-3">Period Rules</h2>

        @foreach($session->sessionPeriods as $period)
        <div class="bg-white border border-gray-200 rounded p-4 mb-3 text-sm">
            <div class="flex justify-between items-start mb-2">
                <span class="font-medium">{{ $period->label ?? 'Period Rule' }}</span>
                <form method="POST" action="{{ route('admin.sessions.destroyPeriod', [$session, $period]) }}">
                    @csrf @method('DELETE')
                    <button class="text-red-500 text-xs hover:underline">Remove</button>
                </form>
            </div>
            <div class="text-gray-500 text-xs space-y-0.5">
                <p>Time In: {{ $period->time_in_start }} — {{ $period->time_in_end }}</p>
                <p>Late Start: {{ $period->late_start }}</p>
                <p>Time Out: {{ $period->time_out_start }} — {{ $period->time_out_end }}</p>
                <p>Grace: {{ $period->grace_minutes }} mins</p>
            </div>
        </div>
        @endforeach

        <div class="bg-white border border-gray-200 rounded p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Add Period Rule</p>
            <form method="POST" action="{{ route('admin.sessions.storePeriod', $session) }}">
                @csrf

                <div class="mb-3">
                    <label class="block text-xs font-medium mb-1">Label (optional)</label>
                    <input type="text" name="label" placeholder="e.g. Morning"
                           class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-gray-400">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Time In Start</label>
                        <input type="time" name="time_in_start"
                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Time In End</label>
                        <input type="time" name="time_in_end"
                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-medium mb-1">Late Start</label>
                    <input type="time" name="late_start"
                           class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div>
                        <label class="block text-xs font-medium mb-1">Time Out Start</label>
                        <input type="time" name="time_out_start"
                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Time Out End</label>
                        <input type="time" name="time_out_end"
                               class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-medium mb-1">Grace Minutes</label>
                    <input type="number" name="grace_minutes" value="0" min="0"
                           class="w-full border border-gray-300 rounded px-2 py-1.5 text-xs">
                </div>

                <div class="mb-3 flex gap-4">
                    <label class="flex items-center gap-1 text-xs">
                        <input type="checkbox" name="late_enabled" value="1" checked>
                        Late enabled
                    </label>
                    <label class="flex items-center gap-1 text-xs">
                        <input type="checkbox" name="timeout_enabled" value="1" checked>
                        Timeout enabled
                    </label>
                </div>

                <button type="submit"
                        class="w-full px-3 py-2 bg-gray-900 text-white text-xs rounded hover:bg-gray-700">
                    Add Rule
                </button>
            </form>
        </div>
    </div>

</div>
@endsection