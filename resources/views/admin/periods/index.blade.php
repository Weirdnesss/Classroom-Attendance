@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Academic Periods</h1>
    <a href="{{ route('admin.periods.create') }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        + Add Period
    </a>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Label</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Term</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Year</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Start</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">End</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($periods as $period)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $period->label }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $period->term->label ?? '--'}}</td>
                <td class="px-4 py-3 text-gray-500">{{ $period->term->academicYear->label ?? '--'}}</td>
                <td class="px-4 py-3 text-gray-500">{{ $period->start_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $period->end_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.periods.edit', $period) }}"
                       class="text-blue-500 hover:underline text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.periods.destroy', $period) }}"
                          class="inline" onsubmit="return confirm('Delete this period?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No periods yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection