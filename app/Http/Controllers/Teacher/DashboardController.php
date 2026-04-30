<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\ClassSchedule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            abort(403, 'No teacher profile found.');
        }

        $today = Carbon::now()->format('D'); // Mon, Tue...

        // Today's schedules
        $todaySchedules = ClassSchedule::where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->whereJsonContains('days', $today)
            ->with(['subject', 'room', 'program'])
            ->get();

        // Today's sessions
        $todaySessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('date', now()->toDateString())
            ->with(['classSchedule.subject', 'room', 'attendanceLogs'])
            ->get();

        // Upcoming sessions this week
        $upcomingSessions = ClassSession::where('teacher_id', $teacher->id)
            ->where('date', '>', now()->toDateString())
            ->where('date', '<=', now()->addDays(7)->toDateString())
            ->with(['classSchedule.subject', 'room'])
            ->orderBy('date')
            ->get();

        return view('teacher.dashboard', compact(
            'teacher', 'todaySchedules', 'todaySessions', 'upcomingSessions'
        ));
    }
}