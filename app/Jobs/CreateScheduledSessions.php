<?php

namespace App\Jobs;

use App\Models\ClassSchedule;
use App\Models\ClassSession;
use App\Models\AttendanceLog;
use App\Models\StudentScheduleEnrollment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateScheduledSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now      = Carbon::now();
        $today    = $now->format('D'); // Mon, Tue, Wed...
        $todayDate = $now->toDateString();

        // Get all active schedules that run today
        $schedules = ClassSchedule::where('is_active', true)
            ->whereJsonContains('days', $today)
            ->with(['academicTerm', 'sessionPeriods'])
            ->get();

        foreach ($schedules as $schedule) {
            // Skip if a session already exists for today
            $exists = ClassSession::where('class_schedule_id', $schedule->id)
                ->where('date', $todayDate)
                ->exists();

            if ($exists) continue;

            // Only create session within 30 mins before start time
            $startTime = Carbon::parse($todayDate . ' ' . $schedule->start_time);
            if ($now->diffInMinutes($startTime, false) > 30) continue;

            // Create the session
            $session = ClassSession::create([
                'class_schedule_id' => $schedule->id,
                'room_id'           => $schedule->room_id,
                'teacher_id'        => $schedule->teacher_id,
                'academic_term_id'  => $schedule->academic_term_id,
                'date'              => $todayDate,
                'started_at'        => $startTime,
                'scan_mode'         => 'in',
                'status'            => 'pending',
                'is_auto_generated' => true,
            ]);

            // Auto-populate attendance logs for enrolled students
            $enrollments = StudentScheduleEnrollment::where('class_schedule_id', $schedule->id)
                ->where('is_active', true)
                ->get();

            foreach ($enrollments as $enrollment) {
                AttendanceLog::create([
                    'class_session_id'   => $session->id,
                    'student_id'         => $enrollment->student_id,
                    'room_id'            => $schedule->room_id,
                    'status'             => 'absent', // default until scanned
                    'is_manual_override' => false,
                ]);
            }
        }
    }
}