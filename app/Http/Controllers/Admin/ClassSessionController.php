<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\ClassSchedule;
use App\Models\SessionPeriod;
use App\Models\AttendanceLog;
use App\Models\StudentScheduleEnrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassSessionController extends Controller
{
    public function index()
    {
        $sessions = ClassSession::with([
            'classSchedule.subject',
            'teacher',
            'room',
            'academicTerm',
        ])->orderByDesc('date')->orderByDesc('started_at')->paginate(20);

        return view('admin.sessions.index', compact('sessions'));
    }

    public function show(ClassSession $session)
    {
        $session->load([
            'classSchedule.subject',
            'classSchedule.program',
            'teacher',
            'room',
            'sessionPeriods',
            'attendanceLogs.student',
        ]);

        return view('admin.sessions.show', compact('session'));
    }

    // Manually create a session (make-up class etc)
    public function create()
    {
        $schedules = ClassSchedule::with(['subject', 'program', 'teacher'])
            ->where('is_active', true)->get();
        return view('admin.sessions.create', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_schedule_id' => 'required|exists:class_schedules,id',
            'date'              => 'required|date',
        ]);

        $schedule = ClassSchedule::findOrFail($request->class_schedule_id);

        $session = ClassSession::create([
            'class_schedule_id' => $schedule->id,
            'room_id'           => $schedule->room_id,
            'teacher_id'        => $schedule->teacher_id,
            'academic_term_id'  => $schedule->academic_term_id,
            'date'              => $request->date,
            'started_at'        => Carbon::parse($request->date . ' ' . $schedule->start_time),
            'scan_mode'         => 'in',
            'status'            => 'pending',
            'is_auto_generated' => false,
        ]);

        // Populate attendance logs
        $enrollments = StudentScheduleEnrollment::where('class_schedule_id', $schedule->id)
            ->where('is_active', true)->get();

        foreach ($enrollments as $enrollment) {
            AttendanceLog::create([
                'class_session_id'   => $session->id,
                'student_id'         => $enrollment->student_id,
                'room_id'            => $schedule->room_id,
                'status'             => 'absent',
                'is_manual_override' => false,
            ]);
        }

        return redirect()->route('admin.sessions.show', $session)
                         ->with('success', 'Session created.');
    }

    // Toggle scan mode in/out
    public function toggleScanMode(ClassSession $session)
    {
        $session->update([
            'scan_mode' => $session->scan_mode === 'in' ? 'out' : 'in',
        ]);

        return back()->with('success', 'Scan mode updated.');
    }

    // Update session status
    public function updateStatus(Request $request, ClassSession $session)
    {
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

    // Add a session period rule
    public function storePeriod(Request $request, ClassSession $session)
    {
        $request->validate([
            'label'           => 'nullable|string',
            'time_in_start'   => 'required',
            'time_in_end'     => 'required',
            'late_start'      => 'required',
            'time_out_start'  => 'required',
            'time_out_end'    => 'required',
            'grace_minutes'   => 'integer|min:0',
            'late_enabled'    => 'boolean',
            'timeout_enabled' => 'boolean',
        ]);

        SessionPeriod::create([
            'class_session_id' => $session->id,
            'label'            => $request->label,
            'time_in_start'    => $request->time_in_start,
            'time_in_end'      => $request->time_in_end,
            'late_start'       => $request->late_start,
            'time_out_start'   => $request->time_out_start,
            'time_out_end'     => $request->time_out_end,
            'grace_minutes'    => $request->grace_minutes ?? 0,
            'late_enabled'     => $request->boolean('late_enabled'),
            'timeout_enabled'  => $request->boolean('timeout_enabled'),
        ]);

        return back()->with('success', 'Period rule added.');
    }

    public function destroyPeriod(ClassSession $session, SessionPeriod $period)
    {
        $period->delete();
        return back()->with('success', 'Period rule removed.');
    }

    // Override a student's attendance manually
    public function overrideAttendance(Request $request, ClassSession $session)
    {
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
}