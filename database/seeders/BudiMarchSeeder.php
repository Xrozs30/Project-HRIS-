<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\LeavePermission;
use App\Models\Reimbursement;
use App\Models\Overtime;
use Carbon\Carbon;

class BudiMarchSeeder extends Seeder
{
    public function run()
    {
        $employeeId = 'ST05';
        $employee = Employee::where('employee_id', $employeeId)->first();

        if (!$employee) {
            $this->command->error('Budi Pekerja (ST05) not found!');
            return;
        }

        $startDate = Carbon::parse('2026-03-01');
        $endDate = Carbon::parse('2026-03-31');

        $lateDays = ['2026-03-05', '2026-03-12'];
        $overtimeDays = ['2026-03-18', '2026-03-19'];
        $cutiDay = '2026-03-24';
        $absenceDay = '2026-03-27';
        $reimburseDay = '2026-03-10';

        // Clear existing data for Budi in March 2026 to avoid duplicates if re-run
        Presence::where('employee_id', $employeeId)->whereBetween('presence_date', [$startDate, $endDate])->delete();
        LeavePermission::where('employee_id', $employeeId)->whereBetween('leave_start_date', [$startDate, $endDate])->delete();
        Reimbursement::where('employee_id', $employeeId)->whereBetween('reimburse_date', [$startDate, $endDate])->delete();
        Overtime::where('employee_id', $employeeId)->whereBetween('overtime_date', [$startDate, $endDate])->delete();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekend()) {
                continue;
            }

            $dateStr = $date->format('Y-m-d');

            if ($dateStr === $cutiDay) {
                // Insert Cuti
                LeavePermission::create([
                    'employee_id' => $employeeId,
                    'leave_type' => 'cuti',
                    'leave_reason' => 'Keperluan keluarga',
                    'leave_start_date' => $cutiDay,
                    'leave_end_date' => $cutiDay,
                    'leave_duration' => 1,
                    'leave_status' => 'approved',
                    'leave_create_at' => Carbon::parse($cutiDay)->subDays(3),
                    'leave_update_at' => Carbon::parse($cutiDay)->subDays(2),
                ]);
                continue;
            }

            if ($dateStr === $absenceDay) {
                // Just skip presence creation for absence
                continue;
            }

            // Normal Presence
            $timeIn = '08:50:00';
            if (in_array($dateStr, $lateDays)) {
                $timeIn = '09:50:00'; // Late
            }

            $timeOut = '17:00:00';
            if (in_array($dateStr, $overtimeDays)) {
                $timeOut = '19:30:00'; // Late checkout due to overtime
                
                // Insert Overtime
                Overtime::create([
                    'employee_id' => $employeeId,
                    'overtime_date' => $dateStr,
                    'overtime_start' => '17:00:00',
                    'overtime_finish' => '19:30:00',
                    'overtime_status' => 'approved',
                    'overtime_create_at' => Carbon::parse($dateStr),
                    'overtime_update_at' => Carbon::parse($dateStr),
                ]);
            }

            $status = 'on_time';
            if (in_array($dateStr, $lateDays)) {
                $status = 'late';
            }

            Presence::create([
                'employee_id' => $employeeId,
                'presence_date' => $dateStr,
                'presence_time_in' => $timeIn,
                'presence_time_out' => $timeOut,
                'presence_photo_in' => 'dummy_photo_in.jpg',
                'presence_photo_out' => 'dummy_photo_out.jpg',
                'presence_status' => $status,
                'presence_create_at' => Carbon::parse($dateStr . ' ' . $timeIn),
                'presence_update_at' => Carbon::parse($dateStr . ' ' . $timeOut),
            ]);
        }

        // Insert Reimbursement
        Reimbursement::create([
            'employee_id' => $employeeId,
            'reimburse_description' => 'Transport meeting client',
            'reimburse_total' => 150000,
            'reimburse_date' => $reimburseDay,
            'reimburse_proof' => 'dummy_receipt.jpg',
            'reimburse_status' => 'approved',
            'reimburse_create_at' => Carbon::parse($reimburseDay),
            'reimburse_update_at' => Carbon::parse($reimburseDay)->addDays(1),
        ]);

        $this->command->info("Dummy data for Budi Pekerja (March 2026) generated successfully!");
    }
}
