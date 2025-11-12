<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ExamController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:auth');
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:auth');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Students
    Route::apiResource('students', StudentController::class);
    Route::post('/students/{student}/link-parent', [StudentController::class, 'linkParent']);

    // Attendance
    Route::post('/attendance/sessions', [AttendanceController::class, 'createSession']);
    Route::post('/attendance/sessions/{session}/mark', [AttendanceController::class, 'markAttendance']);
    Route::post('/attendance/sessions/{session}/lock', [AttendanceController::class, 'lockSession']);
    Route::get('/attendance/reports', [AttendanceController::class, 'getReports']);

    // Exams
    Route::apiResource('exams', ExamController::class);
    Route::post('/exams/{exam}/questions', [ExamController::class, 'addQuestions']);
    Route::post('/exams/{exam}/attempt', [ExamController::class, 'attempt']);
    Route::post('/exams/{exam}/submit', [ExamController::class, 'submit']);
    Route::post('/exams/{exam}/grade', [ExamController::class, 'grade']);
});
