<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\ClassSchedule;
use App\Models\Student;
use App\Models\AcademicTerm;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $terms    = AcademicTerm::with('academicYear')->orderBy('label')->get();
        $schedules = ClassSchedule::with(['subject', 'teacher', 'program'])->get();
        $students  = Student::orderBy('last_name')->get();

        return view('admin.reports.index', compact('terms', 'schedules', 'students'));
    }

    // Per session report
    public function session(ClassSession $session)
    {
        $session->load([
            'classSchedule.subject',
            'classSchedule.program',
            'teacher',
            'room',
            'attendanceLogs.student',
        ]);

        return view('admin.reports.session', compact('session'));
    }

    public function exportSession(ClassSession $session)
    {
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

            // Header info
            fputcsv($handle, ['Subject', $session->classSchedule->subject->code . ' — ' . $session->classSchedule->subject->name]);
            fputcsv($handle, ['Teacher', $session->teacher->full_name]);
            fputcsv($handle, ['Program', $session->classSchedule->program->code . ' Year ' . $session->classSchedule->year_level]);
            fputcsv($handle, ['Room', $session->room->code]);
            fputcsv($handle, ['Date', $session->date->format('F d, Y')]);
            fputcsv($handle, []);

            // Column headers
            fputcsv($handle, ['Student ID', 'Last Name', 'First Name', 'Status', 'Time In', 'Time Out', 'Manual Override', 'Remarks']);

            foreach ($session->attendanceLogs as $log) {
                fputcsv($handle, [
                    $log->student->student_id,
                    $log->student->last_name,
                    $log->student->first_name,
                    ucfirst($log->status),
                    $log->time_in  ? $log->time_in->format('h:i A')  : '—',
                    $log->time_out ? $log->time_out->format('h:i A') : '—',
                    $log->is_manual_override ? 'Yes' : 'No',
                    $log->remarks ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Per schedule report
    public function schedule(ClassSchedule $schedule)
    {
        $schedule->load([
            'subject', 'teacher', 'program', 'room',
            'academicTerm.academicYear',
            'enrollments.student',
            'classSessions' => fn($q) => $q->where('status', 'completed')
                ->with('attendanceLogs')
                ->orderBy('date'),
        ]);

        // Build summary per student
        $summary = [];
        foreach ($schedule->enrollments as $enrollment) {
            $student = $enrollment->student;
            $summary[$student->id] = [
                'student'  => $student,
                'present'  => 0,
                'late'     => 0,
                'absent'   => 0,
                'excused'  => 0,
                'total'    => 0,
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

        return view('admin.reports.schedule', compact('schedule', 'summary'));
    }

    public function exportSchedule(ClassSchedule $schedule)
    {
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

            fputcsv($handle, ['Student ID', 'Last Name', 'First Name', 'Present', 'Late', 'Absent', 'Excused', 'Total Sessions', 'Attendance Rate']);

            foreach ($summary as $row) {
                $total   = $row['total'];
                $present = $row['present'] + $row['late'];
                $rate    = $total > 0 ? round($present / $total * 100, 1) . '%' : '—';

                fputcsv($handle, [
                    $row['student']->student_id,
                    $row['student']->last_name,
                    $row['student']->first_name,
                    $row['present'],
                    $row['late'],
                    $row['absent'],
                    $row['excused'],
                    $total,
                    $rate,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Per student report
    public function student(Request $request)
    {
        $query    = $request->get('q');
        $students = collect();
        $student  = null;
        $logs     = collect();
        $summary  = null;

        if ($query) {
            // If a specific student_id is passed, show their report
            if ($request->has('student_id')) {
                $student = Student::findOrFail($request->student_id);

                $logs = \App\Models\AttendanceLog::where('student_id', $student->id)
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
                // Show list of matches
                $students = Student::where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                    ->orWhere('last_name', 'like', "%{$query}%")
                    ->orWhere('student_id', 'like', "%{$query}%");
                })
                ->with('program')
                ->orderBy('last_name')
                ->get();
            }
        }

        return view('admin.reports.student', compact('query', 'students', 'student', 'logs', 'summary'));
    }

    public function exportStudent(Request $request)
    {
        $request->validate(['student_id' => 'required|exists:students,id']);

        $student = Student::findOrFail($request->student_id);

        $logs = \App\Models\AttendanceLog::where('student_id', $student->id)
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
}