<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceScanController;

Route::post('/scan', [AttendanceScanController::class, 'scan']);