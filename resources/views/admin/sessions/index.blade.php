@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Class Sessions</h1>
    <a href="{{ route('admin.sessions.create') }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        + Manual Session
    </a>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Date</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Subject</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Teacher</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Scan Mode</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($sessions as $session)
            <tr>
                <td class="px-4 py-3 text-gray-500">{{ $session->date->format('M d, Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $session->classSchedule->subject->code }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $session->teacher->full_name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $session->room->code }}</td>
                <td class="px-4 py-3">
                    @if($session->scan_mode === 'in')
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">IN</span>
                    @else
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs">OUT</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @php $colors = ['pending' => 'gray', 'active' => 'green', 'completed' => 'blue', 'cancelled' => 'red']; @endphp
                    <span class="px-2 py-0.5 bg-{{ $colors[$session->status] }}-100 text-{{ $colors[$session->status] }}-700 rounded-full text-xs">
                        {{ ucfirst($session->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ $session->is_auto_generated ? 'Auto' : 'Manual' }}
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.sessions.show', $session) }}"
                       class="text-blue-500 hover:underline text-xs">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-6 text-center text-gray-400">No sessions yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-4 py-3 border-t border-gray-100">
        {{ $sessions->links() }}
    </div>
</div>
@endsection