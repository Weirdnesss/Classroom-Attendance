<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-56 bg-white border-r border-gray-200 flex flex-col min-h-screen fixed">
        <div class="px-6 py-5 border-b border-gray-200">
            <span class="font-bold text-sm tracking-wide uppercase text-gray-700">Attendance</span>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 text-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mb-1">Academic</p>
            <a href="{{ route('admin.academic-years.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Academic Years</a>
            <a href="{{ route('admin.terms.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Terms</a>
            <a href="{{ route('admin.periods.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Periods</a>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Curriculum</p>
            <a href="{{ route('admin.departments.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Departments</a>
            <a href="{{ route('admin.programs.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Programs</a>
            <a href="{{ route('admin.subjects.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Subjects</a>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Infrastructure</p>
            <a href="{{ route('admin.rooms.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Rooms</a>
            <a href="{{ route('admin.devices.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Devices</a>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">People</p>
            <a href="{{ route('admin.teachers.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Teachers</a>
            <a href="{{ route('admin.students.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Students</a>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Scheduling</p>
            <a href="{{ route('admin.schedules.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Class Schedules</a>
        
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Sessions</p>
            <a href="{{ route('admin.sessions.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Class Sessions</a>
        </nav>

        <div class="px-4 py-4 border-t border-gray-200 text-sm">
            <span class="block text-gray-500 mb-2">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-red-500 hover:underline">Logout</button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="ml-56 flex-1 p-8">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded text-sm">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>