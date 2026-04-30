<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;

class ScheduleController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;

        $schedules = ClassSchedule::where('teacher_id', $teacher->id)
            ->with(['subject', 'room', 'program', 'academicTerm.academicYear'])
            ->withCount('enrollments')
            ->latest()
            ->get();

        return view('teacher.schedules.index', compact('schedules'));
    }

    public function show(ClassSchedule $schedule)
    {
        $teacher = auth()->user()->teacher;

        if ($schedule->teacher_id !== $teacher->id) {
            abort(403);
        }

        $schedule->load([
            'subject', 'room', 'program',
            'academicTerm.academicYear',
            'enrollments.student',
            'classSessions' => fn($q) => $q->orderByDesc('date')->take(10),
        ]);

        return view('teacher.schedules.show', compact('schedule'));
    }
}