<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboard;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\DisplayController;

Route::get('/test-scan', function () {
    return view('test-scan');
});
Route::get('/display/room/{room}', [\App\Http\Controllers\DisplayController::class, 'show'])->name('display.room');
Route::get('/display/room/{room}/data', [\App\Http\Controllers\DisplayController::class, 'data'])->name('display.room.data');

Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    return match(auth()->user()->role) {
        'admin'   => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('teacher.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        default   => redirect()->route('login'),
    };
});

// Admin
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::resource('academic-years', \App\Http\Controllers\Admin\AcademicYearController::class);
        Route::resource('terms', \App\Http\Controllers\Admin\AcademicTermController::class);
        Route::resource('periods', \App\Http\Controllers\Admin\AcademicPeriodController::class);
        Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class);
        Route::resource('programs', \App\Http\Controllers\Admin\ProgramController::class);
        Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class);
        Route::resource('rooms', \App\Http\Controllers\Admin\RoomController::class);
        Route::resource('devices', \App\Http\Controllers\Admin\DeviceController::class);
        Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
        Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
        Route::resource('schedules', \App\Http\Controllers\Admin\ClassScheduleController::class);
        Route::post('schedules/{schedule}/enroll', [\App\Http\Controllers\Admin\ClassScheduleController::class, 'enroll'])->name('schedules.enroll');
        Route::delete('schedules/{schedule}/unenroll', [\App\Http\Controllers\Admin\ClassScheduleController::class, 'unenroll'])->name('schedules.unenroll');
        Route::resource('sessions', \App\Http\Controllers\Admin\ClassSessionController::class)->only(['index', 'show', 'create', 'store']);
        Route::post('sessions/{session}/toggle-scan-mode', [\App\Http\Controllers\Admin\ClassSessionController::class, 'toggleScanMode'])->name('sessions.toggleScanMode');
        Route::post('sessions/{session}/status', [\App\Http\Controllers\Admin\ClassSessionController::class, 'updateStatus'])->name('sessions.updateStatus');
        Route::post('sessions/{session}/periods', [\App\Http\Controllers\Admin\ClassSessionController::class, 'storePeriod'])->name('sessions.storePeriod');
        Route::delete('sessions/{session}/periods/{period}', [\App\Http\Controllers\Admin\ClassSessionController::class, 'destroyPeriod'])->name('sessions.destroyPeriod');
        Route::post('sessions/{session}/override', [\App\Http\Controllers\Admin\ClassSessionController::class, 'overrideAttendance'])->name('sessions.override');
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/session/{session}', [\App\Http\Controllers\Admin\ReportController::class, 'session'])->name('reports.session');
        Route::get('reports/session/{session}/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportSession'])->name('reports.session.export');
        Route::get('reports/schedule/{schedule}', [\App\Http\Controllers\Admin\ReportController::class, 'schedule'])->name('reports.schedule');
        Route::get('reports/schedule/{schedule}/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportSchedule'])->name('reports.schedule.export');
        Route::get('reports/student', [\App\Http\Controllers\Admin\ReportController::class, 'student'])->name('reports.student');
        Route::get('reports/student/export', [\App\Http\Controllers\Admin\ReportController::class, 'exportStudent'])->name('reports.student.export');
    });

// Teacher
Route::middleware(['auth', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {
        Route::get('/dashboard', [TeacherDashboard::class, 'index'])->name('dashboard');
        Route::resource('schedules', \App\Http\Controllers\Teacher\ScheduleController::class)->only(['index', 'show']);
        Route::resource('sessions', \App\Http\Controllers\Teacher\SessionController::class)->only(['index', 'show']);
        Route::post('sessions/{session}/toggle-scan-mode', [\App\Http\Controllers\Teacher\SessionController::class, 'toggleScanMode'])->name('sessions.toggleScanMode');
        Route::post('sessions/{session}/status', [\App\Http\Controllers\Teacher\SessionController::class, 'updateStatus'])->name('sessions.updateStatus');
        Route::post('sessions/{session}/override', [\App\Http\Controllers\Teacher\SessionController::class, 'overrideAttendance'])->name('sessions.override');
        Route::get('reports', [\App\Http\Controllers\Teacher\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/session/{session}', [\App\Http\Controllers\Teacher\ReportController::class, 'session'])->name('reports.session');
        Route::get('reports/session/{session}/export', [\App\Http\Controllers\Teacher\ReportController::class, 'exportSession'])->name('reports.session.export');
        Route::get('reports/schedule/{schedule}', [\App\Http\Controllers\Teacher\ReportController::class, 'schedule'])->name('reports.schedule');
        Route::get('reports/schedule/{schedule}/export', [\App\Http\Controllers\Teacher\ReportController::class, 'exportSchedule'])->name('reports.schedule.export');
        Route::get('reports/student', [\App\Http\Controllers\Teacher\ReportController::class, 'student'])->name('reports.student');
        Route::get('reports/student/export', [\App\Http\Controllers\Teacher\ReportController::class, 'exportStudent'])->name('reports.student.export');
    });

// Student
Route::middleware(['auth', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');
    });

// Public TV display
Route::get('/display/room/{room}', [DisplayController::class, 'show'])
    ->name('display.room');

require __DIR__.'/auth.php';