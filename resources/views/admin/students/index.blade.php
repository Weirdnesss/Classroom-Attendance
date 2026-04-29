@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Students</h1>
    <a href="{{ route('admin.students.create') }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        + Add Student
    </a>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Student ID</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Program</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Year</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">RFID</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Type</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($students as $student)
            <tr>
                <td class="px-4 py-3 font-mono font-medium">{{ $student->student_id }}</td>
                <td class="px-4 py-3">{{ $student->full_name }}</td>
                <td class="px-4 py-3 text-gray-500">
                    {{ $student->program->code ?? '—' }}
                </td>
                <td class="px-4 py-3 text-gray-500">Year {{ $student->year_level }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $student->rfid_tag ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($student->is_irregular)
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs">Irregular</span>
                    @else
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">Regular</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    @if($student->is_active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                    @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.students.edit', $student) }}"
                       class="text-blue-500 hover:underline text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.students.destroy', $student) }}"
                          class="inline" onsubmit="return confirm('Delete this student?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-6 text-center text-gray-400">No students yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection