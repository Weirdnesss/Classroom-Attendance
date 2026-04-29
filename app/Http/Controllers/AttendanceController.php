<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\AttendanceLog;
use Carbon\Carbon;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function scan(Request $request)
    {
        $user = User::find($request->user_id);

        $session = Session::where('room_id', $request->room_id)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->first();

        if (!$session) {
            return response()->json(['error' => 'No active session'], 404);
        }

        AttendanceLog::create([
            'user_id' => $user->id,
            'session_id' => $session->id,
            'timestamp' => now(),
            'method' => $request->method ?? 'qr'
        ]);

        return response()->json(['success' => true]);
    }
}
