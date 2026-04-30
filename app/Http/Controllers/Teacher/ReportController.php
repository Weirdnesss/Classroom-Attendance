<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $teacher   = auth()->user()->teacher;
        $schedules = ClassSchedule::where('teacher_id', $teacher->id)
            ->with(['subject', 'program'])
            ->get();

        return view('teacher.reports.index', compact('schedules'));
    }

    public function session(ClassSession $session)
    {
        $this->authorizeTeacher($session);

        $session->load([
            'classSchedule.subject',
            'classSchedule.program',
            'teacher',
            'room',
            'attendanceLogs.student',
        ]);

        return view('teacher.reports.session', compact('session'));
    }

    public function exportSession(ClassSession $session)
    {
        $this->authorizeTeacher($session);

        $session->load([
            'classSchedule.subject',
            'classSchedule.program',
            'teacher',
            'room',
            'attendanceLogs.student',
        ]);

        $filename = 'session_' . $session->classSchedule->subject->code . '_' . $session->date->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($session) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Subject', $session->classSchedule->subject->code . ' — ' . $session->classSchedule->subject->name]);
            fputcsv($handle, ['Teacher', $session->teacher->full_name]);
            fputcsv($handle, ['Program', $session->classSchedule->program->code . ' Year ' . $session->classSchedule->year_level]);
            fputcsv($handle, ['Room', $session->room->code]);
            fputcsv($handle, ['Date', $session->date->format('F d, Y')]);
            fputcsv($handle, []);

            fputcsv($handle, ['Student ID', 'Last Name', 'First Name', 'Status', 'Time In', 'Time Out', 'Manual Override']);

            foreach ($session->attendanceLogs->sortBy('student.last_name') as $log) {
                fputcsv($handle, [
                    $log->student->student_id,
                    $log->student->last_name,
                    $log->student->first_name,
                    ucfirst($log->status),
                    $log->time_in  ? $log->time_in->format('h:i A')  : '—',
                    $log->time_out ? $log->time_out->format('h:i A') : '—',
                    $log->is_manual_override ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function schedule(ClassSchedule $schedule)
    {
        $this->authorizeSchedule($schedule);

        $schedule->load([
            'subject', 'teacher', 'program', 'room',
            'academicTerm.academicYear',
            'enrollments.student',
            'classSessions' => fn($q) => $q->where('status', 'completed')
                ->with('attendanceLogs')->orderBy('date'),
        ]);

        $summary = [];
        foreach ($schedule->enrollments as $enrollment) {
            $student = $enrollment->student;
            $summary[$student->id] = [
                'student' => $student,
                'present' => 0,
                'late'    => 0,
                'absent'  => 0,
                'excused' => 0,
                'total'   => 0,
            ];
        }

        foreach ($schedule->classSessions as $session) {
            foreach ($session->attendanceLogs as $log) {
                if (isset($summary[$log->student_id])) {
                    $summary[$log->student_id][$log->status]++;
                    $summary[$log->student_id]['total']++;
                }
            }
        }

        return view('teacher.reports.schedule', compact('schedule', 'summary'));
    }

    public function exportSchedule(ClassSchedule $schedule)
    {
        $this->authorizeSchedule($schedule);

        $schedule->load([
            'subject', 'teacher', 'program',
            'enrollments.student',
            'classSessions' => fn($q) => $q->where('status', 'completed')
                ->with('attendanceLogs')->orderBy('date'),
        ]);

        $summary = [];
        foreach ($schedule->enrollments as $enrollment) {
            $student = $enrollment->student;
            $summary[$student->id] = [
                'student' => $student,
                'present' => 0,
                'late'    => 0,
                'absent'  => 0,
                'excused' => 0,
                'total'   => 0,
            ];
        }

        foreach ($schedule->classSessions as $session) {
            foreach ($session->attendanceLogs as $log) {
                if (isset($summary[$log->student_id])) {
                    $summary[$log->student_id][$log->status]++;
                    $summary[$log->student_id]['total']++;
                }
            }
        }

        $filename = 'schedule_' . $schedule->subject->code . '_report.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($schedule, $summary) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Subject', $schedule->subject->code . ' — ' . $schedule->subject->name]);
            fputcsv($handle, ['Teacher', $schedule->teacher->full_name]);
            fputcsv($handle, ['Program', $schedule->program->code . ' Year ' . $schedule->year_level]);
            fputcsv($handle, ['Total Sessions', $schedule->classSessions->count()]);
            fputcsv($handle, []);

            fputcsv($handle, ['Student ID', 'Last Name', 'First Name', 'Present', 'Late', 'Absent', 'Excused', 'Total', 'Rate']);

            foreach ($summary as $row) {
                $attended = $row['present'] + $row['late'];
                $rate     = $row['total'] > 0 ? round($attended / $row['total'] * 100, 1) . '%' : '—';

                fputcsv($handle, [
                    $row['student']->student_id,
                    $row['student']->last_name,
                    $row['student']->first_name,
                    $row['present'],
                    $row['late'],
                    $row['absent'],
                    $row['excused'],
                    $row['total'],
                    $rate,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function student(Request $request)
    {
        $teacher     = $this->getTeacher();
        $scheduleIds = ClassSchedule::where('teacher_id', $teacher->id)->pluck('id');

        $query    = $request->get('q');
        $students = collect();
        $student  = null;
        $logs     = collect();
        $summary  = null;

        if ($query) {
            if ($request->has('student_id')) {
                $student = Student::findOrFail($request->student_id);

                $logs = AttendanceLog::where('student_id', $student->id)
                    ->whereHas('classSession', fn($q) => $q->where('teacher_id', $teacher->id))
                    ->with([
                        'classSession.classSchedule.subject',
                        'classSession.teacher',
                    ])
                    ->orderByDesc('created_at')
                    ->get();

                $summary = [
                    'present' => $logs->where('status', 'present')->count(),
                    'late'    => $logs->where('status', 'late')->count(),
                    'absent'  => $logs->where('status', 'absent')->count(),
                    'excused' => $logs->where('status', 'excused')->count(),
                    'total'   => $logs->count(),
                ];
            } else {
                $students = Student::where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('student_id', 'like', "%{$query}%");
                })
                ->whereHas('enrollments', fn($q) => $q->whereIn('class_schedule_id', $scheduleIds))
                ->with('program')
                ->orderBy('last_name')
                ->get();
            }
        }

        return view('teacher.reports.student', compact('query', 'students', 'student', 'logs', 'summary'));
    }

    public function exportStudent(Request $request)
    {
        $request->validate(['student_id' => 'required|exists:students,id']);

        $teacher  = auth()->user()->teacher;
        $student  = Student::findOrFail($request->student_id);

        $logs = AttendanceLog::where('student_id', $student->id)
            ->whereHas('classSession', fn($q) => $q->where('teacher_id', $teacher->id))
            ->with([
                'classSession.classSchedule.subject',
                'classSession.teacher',
            ])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'student_' . $student->student_id . '_attendance.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($student, $logs) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Student ID', $student->student_id]);
            fputcsv($handle, ['Name', $student->full_name]);
            fputcsv($handle, ['Program', $student->program->code ?? '—']);
            fputcsv($handle, ['Year Level', 'Year ' . $student->year_level]);
            fputcsv($handle, []);

            fputcsv($handle, ['Date', 'Subject', 'Teacher', 'Status', 'Time In', 'Time Out']);

            foreach ($logs as $log) {
                fputcsv($handle, [
                    $log->classSession->date->format('F d, Y'),
                    $log->classSession->classSchedule->subject->code,
                    $log->classSession->teacher->full_name,
                    ucfirst($log->status),
                    $log->time_in  ? $log->time_in->format('h:i A')  : '—',
                    $log->time_out ? $log->time_out->format('h:i A') : '—',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function authorizeTeacher(ClassSession $session): void
    {
        if ($session->teacher_id !== auth()->user()->teacher->id) {
            abort(403);
        }
    }

    private function authorizeSchedule(ClassSchedule $schedule): void
    {
        if ($schedule->teacher_id !== auth()->user()->teacher->id) {
            abort(403);
        }
    }

        private function getTeacher()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            abort(403, 'No teacher profile linked to this account.');
        }

        return $teacher;
    }
}