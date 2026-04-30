<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — Teacher</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen flex">

    <aside class="w-56 bg-white border-r border-gray-200 flex flex-col min-h-screen fixed">
        <div class="px-6 py-5 border-b border-gray-200">
            <span class="font-bold text-sm tracking-wide uppercase text-gray-700">Attendance</span>
        </div>

        <nav class="flex-1 px-4 py-4 space-y-1 text-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mb-1">Overview</p>
            <a href="{{ route('teacher.dashboard') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Dashboard</a>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Classes</p>
            <a href="{{ route('teacher.schedules.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">My Schedules</a>
            <a href="{{ route('teacher.sessions.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">My Sessions</a>
            
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2 mt-4 mb-1">Reports</p>
            <a href="{{ route('teacher.reports.index') }}" class="block px-2 py-1.5 rounded hover:bg-gray-100">Reports</a>
        </nav>

        <div class="px-4 py-4 border-t border-gray-200 text-sm">
            <span class="block text-gray-500 mb-2">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-red-500 hover:underline">Logout</button>
            </form>
        </div>
    </aside>

    <main class="ml-56 flex-1 p-8">
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