<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Student;
use App\Models\ClassSession;
use App\Models\AttendanceLog;
use App\Models\SessionPeriod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceScanController extends Controller
{
    public function scan(Request $request)
    {   
        
        $request->validate([
            'rfid_tag'   => 'required|string',
            'device_uid' => 'required|string',
        ]);

        // 1. Resolve device
        $device = Device::where('device_uid', $request->device_uid)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device not recognized.'], 404);
        }

        $device->update(['last_seen_at' => now()]);

        // 2. Decode RFID tag
        $rfid = $request->rfid_tag;

        if (strlen($rfid) < 6) {
            return response()->json(['error' => 'Invalid RFID format.'], 400);
        }

        $studentId = substr($rfid, 5); // everything after position 5
        
        // 3. Resolve active session for this device's room
        $session = ClassSession::where('room_id', $device->room_id)
        ->whereDate('date', now()->toDateString())
        ->where('status', 'active')
        ->first();

        if (!$session) {
        return response()->json(['error' => 'No active session for this device.'], 404);
        }

        // $debugSession = ClassSession::where('room_id', $device->room_id)->get();
        // return response()->json([
        //     'device_room_id'  => $device->room_id,
        //     'today'           => now()->toDateString(),
        //     'all_sessions'    => $debugSession,
        // ]);

        // 4. Resolve student
        $student = Student::where('student_id', $studentId)
            ->where('is_active', true)
            ->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found: ' . $studentId], 404);
        }

        // 5. Find the attendance log for this student
        $log = AttendanceLog::where('class_session_id', $session->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$log) {
            return response()->json(['error' => 'Student not enrolled in this session.'], 403);
        }

        $now = Carbon::now();

        // 6. Handle scan based on mode
        if ($session->scan_mode === 'in') {
            if ($log->time_in) {
                return response()->json([
                    'message' => 'Already scanned in.',
                    'student' => $student->full_name,
                    'status'  => $log->status,
                ]);
            }

            $status = $this->resolveStatus($session, $now);

            $log->update([
                'time_in'   => $now,
                'status'    => $status,
                'device_id' => $device->id,
            ]);

            return response()->json([
                'message' => 'Scan in recorded.',
                'student' => $student->full_name,
                'status'  => $status,
                'time'    => $now->format('h:i A'),
            ]);

        } else {
            // Scan out
            $time = $now->format('H:i:s');

            $period = SessionPeriod::where('class_session_id', $session->id)
                ->where('timeout_enabled', true)
                ->where('time_out_start', '<=', $time)
                ->where('time_out_end', '>=', $time)
                ->first();
        
            if (!$period) {
                return response()->json([
                    'error' => 'No active scan-out window right now.'
                ], 400);
            }
        
            if (!$log->time_in) {
                return response()->json([
                    'error' => 'Student has not scanned in yet.'
                ], 400);
            }
        
            if ($log->time_out) {
                return response()->json([
                    'message' => 'Already scanned out.',
                    'student' => $student->full_name,
                ]);
            }
        
            $log->update([
                'time_out'  => $now,
                'device_id' => $device->id,
            ]);
        
            return response()->json([
                'message' => 'Scan out recorded.',
                'student' => $student->full_name,
                'time'    => $now->format('h:i A'),
            ]);
        }
    }

    private function resolveStatus(ClassSession $session, Carbon $now): string
    {
        $time = $now->format('H:i:s');

        // Find the matching period by current time window
        $period = SessionPeriod::where('class_session_id', $session->id)
            ->where('time_in_start', '<=', $time)
            ->where('time_in_end', '>=', $time)
            ->first();

        if (!$period) {
            return 'present'; // no matching period — default
        }

        if (!$period->late_enabled || !$period->late_start) {
            return 'present';
        }

        $lateStart = Carbon::parse($period->late_start);
        $cutoff    = $lateStart->copy()->addMinutes($period->grace_minutes ?? 0);

        return $now->greaterThan($cutoff) ? 'late' : 'present';
    }
}