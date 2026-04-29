@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-xl font-semibold">Dashboard</h1>
        <p class="text-sm text-gray-500">Welcome back, {{ auth()->user()->name }}</p>
    </div>

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Teachers</p>
            <p class="text-2xl font-bold mt-1">{{ \App\Models\Teacher::count() }}</p>
        </div>
        <div class="bg-white rounded border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Students</p>
            <p class="text-2xl font-bold mt-1">{{ \App\Models\Student::count() }}</p>
        </div>
        <div class="bg-white rounded border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Schedules</p>
            <p class="text-2xl font-bold mt-1">{{ \App\Models\ClassSchedule::count() }}</p>
        </div>
        <div class="bg-white rounded border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Active Sessions</p>
            <p class="text-2xl font-bold mt-1">{{ \App\Models\ClassSession::where('status','active')->count() }}</p>
        </div>
    </div>
@endsection