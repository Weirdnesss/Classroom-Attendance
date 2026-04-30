@extends('layouts.teacher')

@section('content')
<div class="mb-6">
    <a href="{{ route('teacher.schedules.index') }}" class="text-sm text-gray-500 hover:underline">← Back</a>
    <h1 class="text-xl font-semibold mt-1">{{ $schedule->subject->code }} — {{ $schedule->subject->name }}</h1>
    <p class="text-sm text-gray-500 mt-1">
        {{ $schedule->program->code }} Year {{ $schedule->year_level }} &middot;
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
                        <th class="text-left px-4 py-2 font-medium text-gray-600">ID</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedule->enrollments as $enrollment)
                    <tr>
                        <td class="px-4 py-2">{{ $enrollment->student->full_name }}</td>
                        <td class="px-4 py-2 text-gray-500 font-mono text-xs">{{ $enrollment->student->student_id }}</td>
                        <td class="px-4 py-2">
                            @if($enrollment->enrollment_type === 'auto')
                                <span class="text-xs text-blue-500">Auto</span>
                            @else
                                <span class="text-xs text-yellow-600">Manual</span>
                            @endif
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

    {{-- Recent Sessions --}}
    <div>
        <h2 class="text-sm font-semibold mb-3">Recent Sessions</h2>
        <div class="bg-white border border-gray-200 rounded overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Date</th>
                        <th class="text-left px-4 py-2 font-medium text-gray-600">Status</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($schedule->classSessions as $session)
                    <tr>
                        <td class="px-4 py-2 text-gray-500">{{ $session->date->format('M d, Y') }}</td>
                        <td class="px-4 py-2">
                            @php $colors = ['pending'=>'gray','active'=>'green','completed'=>'blue','cancelled'=>'red']; @endphp
                            <span class="px-2 py-0.5 bg-{{ $colors[$session->status] }}-100 text-{{ $colors[$session->status] }}-700 rounded-full text-xs">
                                {{ ucfirst($session->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('teacher.sessions.show', $session) }}"
                               class="text-blue-500 hover:underline text-xs">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-400">No sessions yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection