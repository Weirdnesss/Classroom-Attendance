@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold">Devices</h1>
    <a href="{{ route('admin.devices.create') }}"
       class="px-4 py-2 bg-gray-900 text-white text-sm rounded hover:bg-gray-700">
        + Add Device
    </a>
</div>

<div class="bg-white border border-gray-200 rounded overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Device UID</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Room</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Last Seen</th>
                <th class="text-left px-4 py-3 font-medium text-gray-600">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($devices as $device)
            <tr>
                <td class="px-4 py-3 font-mono font-medium">{{ $device->device_uid }}</td>
                <td class="px-4 py-3">{{ $device->name ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $device->room->code ?? '—' }}</td>
                <td class="px-4 py-3 text-gray-500">
                    {{ $device->last_seen_at ? $device->last_seen_at->diffForHumans() : '—' }}
                </td>
                <td class="px-4 py-3">
                    @if($device->is_active)
                        <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Active</span>
                    @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Inactive</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right space-x-2">
                    <a href="{{ route('admin.devices.edit', $device) }}"
                       class="text-blue-500 hover:underline text-xs">Edit</a>
                    <form method="POST" action="{{ route('admin.devices.destroy', $device) }}"
                          class="inline" onsubmit="return confirm('Delete this device?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:underline text-xs">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-6 text-center text-gray-400">No devices yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection