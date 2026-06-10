<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LeavePermissionController;
use App\Http\Controllers\HrLeaveController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\HrOvertimeController;
use App\Http\Controllers\ReimbursementController;
use App\Http\Controllers\HrReimbursementController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/debug-face', function () {
    $users = \App\Models\Employee::where('employee_role', 'employee')->get();
    $data = [];
    foreach ($users as $user) {
        $data[] = [
            'id' => $user->employee_id,
            'name' => $user->employee_name,
            'length' => strlen((string)$user->employee_face_descriptor),
            'preview' => substr((string)$user->employee_face_descriptor, 0, 50),
        ];
    }
    return response()->json($data);
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class , 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class , 'login']);
});
Route::post('/logout', [LoginController::class , 'logout'])->name('logout');

Route::middleware(['auth', \App\Http\Middleware\CheckFaceEnrollment::class])->group(function () {
    Route::get('/dashboard', function () {
            if (auth()->user()->employee_role == 'employee') {
                return view('dashboard.employee');
            }

            $today = \Carbon\Carbon::today()->toDateString();

            // Total Employees
            $totalEmployees = \App\Models\Employee::where('employee_role', 'employee')->count();

            // Present Today
            $presentToday = \App\Models\Presence::where('presence_date', $today)
                ->where('presence_status', '!=', 'Alpa')
                ->distinct('employee_id')
                ->count();

            // On Leave
            $onLeave = \App\Models\LeavePermission::where('leave_status', 'approved')
                ->where('leave_start_date', '<=', $today)
                ->where('leave_end_date', '>=', $today)
                ->count();

            // Estimasi Payroll (Sum of basic_salary)
            $estimasiPayroll = \App\Models\Employee::where('employee_role', 'employee')->sum('employee_basic_salary');

            // Latest Attendance
            $latestAttendances = \App\Models\Presence::with('employee')
                ->where('presence_date', $today)
                ->orderBy('presence_time_in', 'desc')
                ->take(5)
                ->get();

            // Pending HR Actions
            $pendingLeaves = \App\Models\LeavePermission::where('leave_status', 'pending')->count();
            $pendingOvertimes = \App\Models\Overtime::where('overtime_status', 'pending')->count();
            $pendingReimbursements = \App\Models\Reimbursement::where('reimburse_status', 'pending')->count();

            return view('welcome', compact('totalEmployees', 'presentToday', 'onLeave', 'estimasiPayroll', 'latestAttendances', 'pendingLeaves', 'pendingOvertimes', 'pendingReimbursements'));
        }
        )->name('dashboard');

        Route::resource('karyawan', KaryawanController::class);
        Route::get('/presence', [PresenceController::class , 'index'])->name('presence.index');
        Route::get('/presence/create', [PresenceController::class , 'create'])->name('presence.create');
        Route::post('/presence', [PresenceController::class , 'store'])->name('presence.store');
        Route::post('/presence/update', [PresenceController::class , 'update'])->name('presence.update');

        Route::get('/payroll', [PayrollController::class , 'index'])->name('payroll.index');
        Route::get('/payroll/auto-overtime', [PayrollController::class , 'getAutoOvertime'])->name('payroll.autoOvertime');
        Route::post('/payroll/review-batch', [PayrollController::class , 'reviewBatch'])->name('payroll.reviewBatch');
        Route::post('/payroll/batch', [PayrollController::class , 'storeBatch'])->name('payroll.storeBatch');
        Route::get('/payroll/report/{month}/{year}', [PayrollController::class , 'report'])->name('payroll.report');
        Route::get('/payroll/report-pdf/{month}/{year}', [PayrollController::class , 'generateReportPDF'])->name('payroll.report_pdf');
        Route::post('/payroll/approve', [PayrollController::class , 'approveBatch'])->name('payroll.approveBatch');
        Route::get('/payroll/pdf/{month}/{year}', [PayrollController::class , 'generatePDF'])->name('payroll.pdf');

        // Leave Routes
        Route::get('/leave', [LeavePermissionController::class , 'index'])->name('leave.index');
        Route::get('/leave/create', [LeavePermissionController::class , 'create'])->name('leave.create');
        Route::post('/leave', [LeavePermissionController::class , 'store'])->name('leave.store');

        // HR Leave Routes
        Route::prefix('hr')->group(function () {
            Route::get('/leave', [HrLeaveController::class , 'index'])->name('hr.leave.index');
            Route::post('/leave/{id}/approve', [HrLeaveController::class , 'approve'])->name('hr.leave.approve');
            Route::post('/leave/{id}/reject', [HrLeaveController::class , 'reject'])->name('hr.leave.reject');

            Route::get('/overtime', [HrOvertimeController::class , 'index'])->name('hr.overtime.index');
            Route::post('/overtime/{id}/approve', [HrOvertimeController::class , 'approve'])->name('hr.overtime.approve');
            Route::post('/overtime/{id}/reject', [HrOvertimeController::class , 'reject'])->name('hr.overtime.reject');

            Route::get('/reimbursement', [HrReimbursementController::class , 'index'])->name('hr.reimbursement.index');
            Route::get('/reimbursement/batch', [HrReimbursementController::class , 'showMonth'])->name('hr.reimbursement.showMonth');
            Route::post('/reimbursement/batch/approve', [HrReimbursementController::class , 'approveBatch'])->name('hr.reimbursement.approveBatch');
            Route::post('/reimbursement/batch/reject', [HrReimbursementController::class , 'rejectBatch'])->name('hr.reimbursement.rejectBatch');
        }
        );

        // Employee Overtime Routes
        Route::get('/overtime', [OvertimeController::class , 'index'])->name('overtime.index');
        Route::post('/overtime', [OvertimeController::class , 'store'])->name('overtime.store');

        // Employee Reimbursement Routes
        Route::get('/reimbursement', [ReimbursementController::class , 'index'])->name('reimbursement.index');
        Route::get('/reimbursement/create', [ReimbursementController::class , 'create'])->name('reimbursement.create');
        Route::post('/reimbursement', [ReimbursementController::class , 'store'])->name('reimbursement.store');

        // Profile & Face Enrollment Routes
        Route::get('/profile/face', [ProfileController::class , 'faceEnrollment'])->name('profile.face');
        Route::post('/profile/face', [ProfileController::class , 'saveFace'])->name('profile.face.save');
    });