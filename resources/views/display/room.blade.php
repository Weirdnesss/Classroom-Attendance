<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $room->name ?? $room->code }} — Display</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #0f1117; color: #f1f5f9; font-family: system-ui, sans-serif; }
        .status-present  { background: #052e16; color: #4ade80; }
        .status-late     { background: #422006; color: #fb923c; }
        .status-absent   { background: #1f1f1f; color: #6b7280; }
        .status-excused  { background: #1e3a5f; color: #60a5fa; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.3} }
        .pulse { animation: pulse-dot 1.5s infinite; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    {{-- Header --}}
    <div class="flex items-center justify-between px-12 py-6 border-b border-white/10">
        <div>
            <p class="text-sm text-gray-400 uppercase tracking-widest mb-1">{{ $room->name ?? $room->code }}</p>
            <h1 id="subject" class="text-4xl font-bold tracking-tight">
                {{ $session?->classSchedule->subject->code ?? 'No Active Session' }}
            </h1>
            <p id="subject-name" class="text-gray-400 text-lg mt-1">
                {{ $session?->classSchedule->subject->name ?? '' }}
            </p>
        </div>

        <div class="text-right">
            <p id="teacher" class="text-xl text-gray-300">
                {{ $session?->teacher->full_name ?? '' }}
            </p>
            <p id="program" class="text-gray-400 mt-1">
                {{ $session ? $session->classSchedule->program->code . ' Year ' . $session->classSchedule->year_level : '' }}
            </p>
            <div id="scan-mode-wrap" class="mt-2 {{ $session ? '' : 'hidden' }}">
                <span id="scan-mode"
                      class="px-3 py-1 rounded-full text-sm font-bold
                             {{ $session?->scan_mode === 'in' ? 'bg-blue-900 text-blue-300' : 'bg-purple-900 text-purple-300' }}">
                    {{ $session ? strtoupper($session->scan_mode) . ' MODE' : '' }}
                </span>
            </div>
        </div>

        <div class="text-right">
            <p id="clock" class="text-5xl font-mono font-bold tabular-nums"></p>
            <p id="date-lbl" class="text-gray-400 mt-1 text-sm"></p>
            <div class="flex items-center justify-end gap-2 mt-2">
                <span class="pulse w-2 h-2 rounded-full bg-green-400 inline-block"></span>
                <span class="text-xs text-gray-500">Live</span>
            </div>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div id="stats-bar" class="flex divide-x divide-white/10 border-b border-white/10 {{ $session ? '' : 'hidden' }}">
        <div class="flex-1 px-12 py-5">
            <p class="text-sm text-gray-400 uppercase tracking-wider mb-1">Present</p>
            <p id="stat-present" class="text-4xl font-bold text-green-400">
                {{ $session ? $session->attendanceLogs->whereIn('status', ['present','late'])->count() : 0 }}
            </p>
        </div>
        <div class="flex-1 px-12 py-5">
            <p class="text-sm text-gray-400 uppercase tracking-wider mb-1">Late</p>
            <p id="stat-late" class="text-4xl font-bold text-orange-400">
                {{ $session ? $session->attendanceLogs->where('status', 'late')->count() : 0 }}
            </p>
        </div>
        <div class="flex-1 px-12 py-5">
            <p class="text-sm text-gray-400 uppercase tracking-wider mb-1">Absent</p>
            <p id="stat-absent" class="text-4xl font-bold text-gray-500">
                {{ $session ? $session->attendanceLogs->where('status', 'absent')->count() : 0 }}
            </p>
        </div>
        <div class="flex-1 px-12 py-5">
            <p class="text-sm text-gray-400 uppercase tracking-wider mb-1">Total</p>
            <p id="stat-total" class="text-4xl font-bold text-white">
                {{ $session ? $session->attendanceLogs->count() : 0 }}
            </p>
        </div>
    </div>

    {{-- No session state --}}
    <div id="no-session" class="{{ $session ? 'hidden' : '' }} flex-1 flex items-center justify-center">
        <div class="text-center">
            <p class="text-6xl mb-4">...</p> 
            <!-- 📋 -->
            <p class="text-2xl text-gray-400">No active session in this room</p>
            <p class="text-gray-600 mt-2">{{ $room->name ?? $room->code }}</p>
        </div>
    </div>

    {{-- Student Grid --}}
    <div id="student-grid" class="flex-1 overflow-hidden px-12 py-6 {{ $session ? '' : 'hidden' }}">
        <div id="grid" class="grid gap-2" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
            @if($session)
                @foreach($session->attendanceLogs as $log)
                <div class="rounded-lg px-4 py-3 status-{{ $log->status }} flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm leading-tight">{{ $log->student->full_name }}</p>
                        <p class="text-xs opacity-60 mt-0.5">{{ $log->student->student_id }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase">{{ $log->status }}</p>
                        @if($log->time_in)
                            <p class="text-xs opacity-60">{{ $log->time_in->format('h:i A') }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>

</body>

<script>
    // Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('clock').textContent =
            now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('date-lbl').textContent =
            now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Poll for live data every 5 seconds
    async function poll() {
        try {
            const res  = await fetch('{{ route("display.room.data", $room) }}');
            const data = await res.json();

            if (!data.session) {
                document.getElementById('no-session').classList.remove('hidden');
                document.getElementById('stats-bar').classList.add('hidden');
                document.getElementById('student-grid').classList.add('hidden');
                document.getElementById('subject').textContent = 'No Active Session';
                document.getElementById('subject-name').textContent = '';
                document.getElementById('teacher').textContent = '';
                document.getElementById('program').textContent = '';
                document.getElementById('scan-mode-wrap').classList.add('hidden');
                return;
            }

            const s = data.session;

            // Header
            document.getElementById('subject').textContent      = s.subject;
            document.getElementById('subject-name').textContent = s.subject_name;
            document.getElementById('teacher').textContent      = s.teacher;
            document.getElementById('program').textContent      = s.program + ' Year ' + s.year_level;

            const modeEl = document.getElementById('scan-mode');
            modeEl.textContent = s.scan_mode.toUpperCase() + ' MODE';
            modeEl.className = 'px-3 py-1 rounded-full text-sm font-bold ' +
                (s.scan_mode === 'in' ? 'bg-blue-900 text-blue-300' : 'bg-purple-900 text-purple-300');
            document.getElementById('scan-mode-wrap').classList.remove('hidden');

            // Stats
            document.getElementById('stat-present').textContent = s.present;
            document.getElementById('stat-late').textContent    = s.late;
            document.getElementById('stat-absent').textContent  = s.absent;
            document.getElementById('stat-total').textContent   = s.total;

            // Show panels
            document.getElementById('no-session').classList.add('hidden');
            document.getElementById('stats-bar').classList.remove('hidden');
            document.getElementById('student-grid').classList.remove('hidden');

            // Rebuild grid
            const grid = document.getElementById('grid');
            grid.innerHTML = s.logs.map(log => `
                <div class="rounded-lg px-4 py-3 status-${log.status} flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm leading-tight">${log.name}</p>
                        <p class="text-xs opacity-60 mt-0.5">${log.id}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase">${log.status}</p>
                        ${log.time_in ? `<p class="text-xs opacity-60">${log.time_in}</p>` : ''}
                    </div>
                </div>
            `).join('');

        } catch(e) {
            console.error('Poll error:', e);
        }
    }

    poll();
    setInterval(poll, 5000);
</script>
</html>