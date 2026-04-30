<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;

        $sessions = ClassSession::where('teacher_id', $teacher->id)
            ->with(['classSchedule.subject', 'room'])
            ->orderByDesc('date')
            ->paginate(20);

        return view('teacher.sessions.index', compact('sessions'));
    }

    public function show(ClassSession $session)
    {
        $teacher = auth()->user()->teacher;

        if ($session->teacher_id !== $teacher->id) {
            abort(403);
        }

        $session->load([
            'classSchedule.subject',
            'classSchedule.program',
            'room',
            'sessionPeriods',
            'attendanceLogs.student',
        ]);

        return view('teacher.sessions.show', compact('session'));
    }

    public function toggleScanMode(ClassSession $session)
    {
        $this->authorizeTeacher($session);

        $session->update([
            'scan_mode' => $session->scan_mode === 'in' ? 'out' : 'in',
        ]);

        return back()->with('success', 'Scan mode updated.');
    }

    public function updateStatus(Request $request, ClassSession $session)
    {
        $this->authorizeTeacher($session);

        $request->validate([
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'active' && !$session->started_at) {
            $data['started_at'] = now();
        }
        if ($request->status === 'completed' && !$session->ended_at) {
            $data['ended_at'] = now();
        }

        $session->update($data);

        return back()->with('success', 'Session status updated.');
    }

    public function overrideAttendance(Request $request, ClassSession $session)
    {
        $this->authorizeTeacher($session);

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status'     => 'required|in:present,late,absent,excused',
            'remarks'    => 'nullable|string',
        ]);

        AttendanceLog::where('class_session_id', $session->id)
            ->where('student_id', $request->student_id)
            ->update([
                'status'             => $request->status,
                'is_manual_override' => true,
                'remarks'            => $request->remarks,
            ]);

        return back()->with('success', 'Attendance updated.');
    }

    private function authorizeTeacher(ClassSession $session): void
    {
        if ($session->teacher_id !== auth()->user()->teacher->id) {
            abort(403);
        }
    }
}