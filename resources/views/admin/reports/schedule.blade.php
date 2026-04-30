@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.reports.index') }}" class="text-sm text-gray-500 hover:underline">← Reports</a>
        <h1 class="text-xl font-semibold mt-1">
            {{ $schedule->subject->code }} — {{ $schedule->program->code }} Year {{ $schedule->year_level }}
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            {{ $schedule->teacher->full_name }} &middot; {{ $schedule->classSessions->count() }} sessions
        </p>
    </div>
    <a href="{{ route('admin.reports.schedule.export', $schedule) }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        Export CSV
    </a>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Student ID</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Present</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Late</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Absent</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Excused</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Total</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Rate</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($summary as $row)
            @php
                $attended = $row['present'] + $row['late'];
                $rate     = $row['total'] > 0 ? round($attended / $row['total'] * 100, 1) : 0;
            @endphp
            <tr>
                <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $row['student']->student_id }}</td>
                <td class="px-4 py-3 font-medium">{{ $row['student']->full_name }}</td>
                <td class="px-4 py-3 text-green-600 font-medium">{{ $row['present'] }}</td>
                <td class="px-4 py-3 text-yellow-600 font-medium">{{ $row['late'] }}</td>
                <td class="px-4 py-3 text-red-500 font-medium">{{ $row['absent'] }}</td>
                <td class="px-4 py-3 text-blue-500 font-medium">{{ $row['excused'] }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $row['total'] }}</td>
                <td class="px-4 py-3">
                    <span class="font-medium {{ $rate >= 75 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $rate }}%
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-6 text-center text-gray-400">No data yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection