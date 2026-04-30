@extends('layouts.teacher')

@section('content')
<div class="mb-6">
    <h1 class="text-xl font-semibold">My Schedules</h1>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Subject</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Program / Year</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Days</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Students</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($schedules as $schedule)
            <tr>
                <td class="px-4 py-3">
                    <div class="font-medium">{{ $schedule->subject->code }}</div>
                    <div class="text-gray-400 text-xs">{{ $schedule->subject->name }}</div>
                </td>
                <td class="px-4 py-3 text-gray-500">
                    {{ $schedule->program->code }} — Year {{ $schedule->year_level }}
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $schedule->room->code }}</td>
                <td class="px-4 py-3 text-gray-500">{{ implode(', ', $schedule->days) }}</td>
                <td class="px-4 py-3 text-gray-500">
                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} —
                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $schedule->enrollments_count }}</td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('teacher.schedules.show', $schedule) }}"
                       class="text-blue-500 hover:underline text-xs">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-400">No schedules assigned.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection