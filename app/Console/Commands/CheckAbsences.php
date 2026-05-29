<?php

namespace App\Console\Commands;

use App\Models\Absence;
use App\Models\Presence;
use App\Models\Employee;
use App\Models\LeavePermission;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckAbsences extends Command
{
    /**
     * The name and signature of the console command.
     * Usage: php artisan absences:check [--date=YYYY-MM-DD]
     */
    protected $signature = 'absences:check {--date= : The date to check (default: yesterday)}';

    protected $description = 'Check for employee absences on the given working day and record penalties';

    // Default penalty per absent day (can be configured via .env: ABSENCE_PENALTY_AMOUNT)
    protected float $penaltyAmount;

    public function handle()
    {
        $this->penaltyAmount = (float) env('ABSENCE_PENALTY_AMOUNT', 50000);

        $dateStr = $this->option('date') ?: Carbon::yesterday()->toDateString();
        $date = Carbon::parse($dateStr);

        // Skip Sundays (0 = Sunday)
        if ($date->dayOfWeek === Carbon::SUNDAY) {
            $this->info("Skipping Sunday: {$dateStr}");
            return 0;
        }

        $this->info("Checking absences for: {$dateStr}");

        // Get all active employees and HR
        $employees = Employee::whereIn('employee_role', ['employee', 'hr'])->get();

        $absentCount = 0;

        foreach ($employees as $employee) {
            // Check if there's a leave permission for this day
            $onLeave = LeavePermission::where('employee_id', $employee->employee_id)
                ->where('leave_status', 'approved')
                ->where('leave_start_date', '<=', $dateStr)
                ->where('leave_end_date', '>=', $dateStr)
                ->exists();

            if ($onLeave) {
                $this->line("  {$employee->employee_name}: On approved leave, skipping.");
                continue;
            }

            // Check if they have attendance that day
            $hasAttendance = \App\Models\Presence::where('employee_id', $employee->employee_id)
                ->whereDate('presence_date', $dateStr)
                ->exists();

            if (!$hasAttendance) {
                // Check if absence already recorded for this day
                $alreadyRecorded = Absence::where('employee_id', $employee->employee_id)
                    ->where('date', $dateStr)
                    ->exists();

                if (!$alreadyRecorded) {
                    Absence::create([
                        'employee_id'    => $employee->employee_id,
                        'date'           => $dateStr,
                        'penalty_amount' => $this->penaltyAmount,
                        'notes'          => 'Auto-detected: No attendance record or leave on this day.',
                    ]);
                    $this->warn("  ABSENT: {$employee->employee_name} — Penalty: Rp " . number_format($this->penaltyAmount, 0, ',', '.'));
                    $absentCount++;
                } else {
                    $this->line("  {$employee->employee_name}: Already recorded as absent.");
                }
            } else {
                $this->line("  {$employee->employee_name}: Present.");
            }
        }

        $this->info("Done. {$absentCount} new absences recorded for {$dateStr}.");
        return 0;
    }
}
