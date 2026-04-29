<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\AcademicTerm;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Room;
use App\Models\Program;
use App\Models\Student;
use App\Models\StudentScheduleEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassScheduleController extends Controller
{
    public function index()
    {
        $schedules = ClassSchedule::with([
            'academicTerm.academicYear',
            'subject',
            'teacher',
            'room',
            'program',
        ])->withCount('enrollments')->latest()->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $terms    = AcademicTerm::with('academicYear')->orderBy('label')->get();
        $subjects = Subject::where('is_active', true)->orderBy('code')->get();
        $teachers = Teacher::where('is_active', true)->orderBy('last_name')->get();
        $rooms    = Room::where('is_active', true)->orderBy('code')->get();
        $programs = Program::where('is_active', true)->orderBy('code')->get();

        return view('admin.schedules.create', compact(
            'terms', 'subjects', 'teachers', 'rooms', 'programs'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,id',
            'room_id'          => 'required|exists:rooms,id',
            'program_id'       => 'required|exists:programs,id',
            'year_level'       => 'required|integer|min:1|max:6',
            'days'             => 'required|array|min:1',
            'days.*'           => 'in:Mon,Tue,Wed,Thu,Fri,Sat',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
        ]);

        DB::transaction(function () use ($request) {
            $schedule = ClassSchedule::create([
                'academic_term_id' => $request->academic_term_id,
                'subject_id'       => $request->subject_id,
                'teacher_id'       => $request->teacher_id,
                'room_id'          => $request->room_id,
                'program_id'       => $request->program_id,
                'year_level'       => $request->year_level,
                'days'             => $request->days,
                'start_time'       => $request->start_time,
                'end_time'         => $request->end_time,
            ]);

            // Auto-enroll regular students
            $this->autoEnrollStudents($schedule);
        });

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Schedule created and students enrolled.');
    }

    public function show(ClassSchedule $schedule)
    {
        $schedule->load([
            'academicTerm.academicYear',
            'subject', 'teacher', 'room', 'program',
            'enrollments.student',
        ]);

        $unenrolledStudents = Student::where('program_id', $schedule->program_id)
            ->where('year_level', $schedule->year_level)
            ->where('is_active', true)
            ->whereNotIn('id', $schedule->enrollments->pluck('student_id'))
            ->orderBy('last_name')
            ->get();

        return view('admin.schedules.show', compact('schedule', 'unenrolledStudents'));
    }

    public function edit(ClassSchedule $schedule)
    {
        $terms    = AcademicTerm::with('academicYear')->orderBy('label')->get();
        $subjects = Subject::where('is_active', true)->orderBy('code')->get();
        $teachers = Teacher::where('is_active', true)->orderBy('last_name')->get();
        $rooms    = Room::where('is_active', true)->orderBy('code')->get();
        $programs = Program::where('is_active', true)->orderBy('code')->get();

        return view('admin.schedules.edit', compact(
            'schedule', 'terms', 'subjects', 'teachers', 'rooms', 'programs'
        ));
    }

    public function update(Request $request, ClassSchedule $schedule)
    {
        $request->validate([
            'academic_term_id' => 'required|exists:academic_terms,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,id',
            'room_id'          => 'required|exists:rooms,id',
            'program_id'       => 'required|exists:programs,id',
            'year_level'       => 'required|integer|min:1|max:6',
            'days'             => 'required|array|min:1',
            'days.*'           => 'in:Mon,Tue,Wed,Thu,Fri,Sat',
            'start_time'       => 'required',
            'end_time'         => 'required|after:start_time',
            'is_active'        => 'boolean',
        ]);

        $schedule->update([
            'academic_term_id' => $request->academic_term_id,
            'subject_id'       => $request->subject_id,
            'teacher_id'       => $request->teacher_id,
            'room_id'          => $request->room_id,
            'program_id'       => $request->program_id,
            'year_level'       => $request->year_level,
            'days'             => $request->days,
            'start_time'       => $request->start_time,
            'end_time'         => $request->end_time,
            'is_active'        => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Schedule updated.');
    }

    public function destroy(ClassSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')
                         ->with('success', 'Schedule deleted.');
    }

    // Manually enroll a student (for irregulars)
    public function enroll(Request $request, ClassSchedule $schedule)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        StudentScheduleEnrollment::firstOrCreate(
            [
                'student_id'        => $request->student_id,
                'class_schedule_id' => $schedule->id,
            ],
            [
                'enrollment_type' => 'manual',
                'is_active'       => true,
            ]
        );

        return back()->with('success', 'Student enrolled.');
    }

    // Remove a student from schedule
    public function unenroll(Request $request, ClassSchedule $schedule)
    {
        StudentScheduleEnrollment::where('class_schedule_id', $schedule->id)
            ->where('student_id', $request->student_id)
            ->delete();

        return back()->with('success', 'Student removed.');
    }

    // Auto-enroll all regular students for a program + year level
    private function autoEnrollStudents(ClassSchedule $schedule): void
    {
        $students = Student::where('program_id', $schedule->program_id)
            ->where('year_level', $schedule->year_level)
            ->where('is_irregular', false)
            ->where('is_active', true)
            ->get();

        foreach ($students as $student) {
            StudentScheduleEnrollment::firstOrCreate(
                [
                    'student_id'        => $student->id,
                    'class_schedule_id' => $schedule->id,
                ],
                [
                    'enrollment_type' => 'auto',
                    'is_active'       => true,
                ]
            );
        }
    }
}