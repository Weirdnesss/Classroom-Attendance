<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\ClassSession;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    public function show(Room $room)
    {
        $session = ClassSession::where('room_id', $room->id)
            ->whereIn('status', ['pending', 'active'])
            ->where('date', now()->toDateString())
            ->with([
                'classSchedule.subject',
                'classSchedule.program',
                'teacher',
                'sessionPeriods',
                'attendanceLogs.student',
            ])
            ->latest()
            ->first();

        return view('display.room', compact('room', 'session'));
    }

    // JSON endpoint for polling
    public function data(Room $room)
    {
        $session = ClassSession::where('room_id', $room->id)
            ->whereIn('status', ['pending', 'active'])
            ->where('date', now()->toDateString())
            ->with([
                'classSchedule.subject',
                'classSchedule.program',
                'teacher',
                'attendanceLogs.student',
            ])
            ->latest()
            ->first();

        if (!$session) {
            return response()->json(['session' => null]);
        }

        $logs = $session->attendanceLogs->map(fn($log) => [
            'name'     => $log->student->full_name,
            'id'       => $log->student->student_id,
            'status'   => $log->status,
            'time_in'  => $log->time_in?->format('h:i A'),
            'time_out' => $log->time_out?->format('h:i A'),
        ]);

        return response()->json([
            'session' => [
                'subject'    => $session->classSchedule->subject->code,
                'subject_name' => $session->classSchedule->subject->name,
                'teacher'    => $session->teacher->full_name,
                'program'    => $session->classSchedule->program->code,
                'year_level' => $session->classSchedule->year_level,
                'scan_mode'  => $session->scan_mode,
                'status'     => $session->status,
                'present'    => $logs->whereIn('status', ['present', 'late'])->count(),
                'absent'     => $logs->where('status', 'absent')->count(),
                'late'       => $logs->where('status', 'late')->count(),
                'total'      => $logs->count(),
                'logs'       => $logs->values(),
            ],
        ]);
    }
}