@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Class Schedules</h1>
    <a href="{{ route('admin.schedules.create') }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        + Add Schedule
    </a>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Subject</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Teacher</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Program / Year</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Days</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Time</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Students</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
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
                <td class="px-4 py-3 text-gray-500">{{ $schedule->teacher->full_name }}</td>
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
                <td class="px-4 py-3">
                    @if($schedule->is_active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                    @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.schedules.show', $schedule) }}"
                       class="text-green-500 hover:underline text-xs">Students</a>
                    <a href="{{ route('admin.schedules.edit', $schedule) }}"
                       class="text-blue-500 hover:underline text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}"
                          class="inline" onsubmit="return confirm('Delete this schedule?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="px-4 py-6 text-center text-gray-400">No schedules yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection