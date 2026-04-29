@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Academic Terms</h1>
    <a href="{{ route('admin.terms.create') }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        + Add Term
    </a>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Label</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Year</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Start</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">End</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($terms as $term)
            <tr>
                <td class="px-4 py-3 font-medium">{{ $term->label }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $term->academicYear->label }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $term->start_date->format('M d, Y') }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $term->end_date->format('M d, Y') }}</td>
                <td class="px-4 py-3">
                    @if($term->is_active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                    @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.terms.edit', $term) }}"
                       class="text-blue-500 hover:underline text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.terms.destroy', $term) }}"
                          class="inline" onsubmit="return confirm('Delete this term?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No terms yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection