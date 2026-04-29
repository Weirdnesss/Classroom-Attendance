@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.schedules.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">
        {{ $schedule->subject->code }} — {{ $schedule->program->code }} Year {{ $schedule->year_level }}
    </h1>
    <p class="text-sm text-gray-500 mt-1">
        {{ $schedule->teacher->full_name }} &middot;
        {{ $schedule->room->code }} &middot;
        {{ implode(', ', $schedule->days) }} &middot;
        {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} —
        {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
    </p>
</div>

<div class="grid grid-cols-2 gap-6">

    {{-- Enrolled Students --}}
    <div>
        <h2 class="text-sm font-semibold mb-3">Enrolled Students ({{ $schedule->enrollments->count() }})</h2>
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Type</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedule->enrollments as $enrollment)
                    <tr>
                        <td class="px-4 py-2">{{ $enrollment->student->full_name }}</td>
                        <td class="px-4 py-2">
                            @if($enrollment->enrollment_type === 'auto')
                                <span class="text-xs text-blue-500">Auto</span>
                            @else
                                <span class="text-xs text-yellow-600">Manual</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right">
                            <form method="POST"
                                  action="{{ route('admin.schedules.unenroll', $schedule) }}"
                                  onsubmit="return confirm('Remove this student?')">
                                @csrf @method('DELETE')
                                <input type="hidden" name="student_id" value="{{ $enrollment->student_id }}">
                                <button class="text-red-500 hover:underline text-xs">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-400">No students enrolled.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Irregular Students --}}
    <div>
        <h2 class="text-sm font-semibold mb-3">Add Student Manually</h2>
        <div class="bg-white border border-gray-200 rounded p-4">
            @if($unenrolledStudents->isEmpty())
                <p class="text-sm text-gray-400">All eligible students are enrolled.</p>
            @else
                <form method="POST" action="{{ route('admin.schedules.enroll', $schedule) }}">
                    @csrf
                    <select name="student_id"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-3 focus:outline-none focus:ring-1 focus:ring-gray-400">
                        <option value="">— Select Student —</option>
                        @foreach($unenrolledStudents as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->full_name }} ({{ $student->student_id }})
                                @if($student->is_irregular) — Irregular @endif
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700 w-full">
                        Enroll Student
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection