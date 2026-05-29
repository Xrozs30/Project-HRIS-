<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Presence;
use App\Models\Overtime;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class GenerateDummyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-dummy-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy data for testing purposes (Employees, Attendance, Leaves, Overtime, Payroll) from August to January.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting dummy data generation...');

        DB::beginTransaction();
        try {
            // 1. Get an existing employee to copy face descriptors from
            $referenceEmployee = User::where('employee_role', 'employee')->whereNotNull('face_descriptor')->first();
            $faceDescriptor = $referenceEmployee ? $referenceEmployee->face_descriptor : null;
            $faceImage = $referenceEmployee ? $referenceEmployee->face_image : null;

            // 2. Create 2 new employees
            $this->info('Creating 2 new employees...');
            $newEmployees = [];
            for ($i = 1; $i <= 2; $i++) {
                $newEmployees[] = User::firstOrCreate(
                ['email' => 'dummy' . $i . '@test.com'],
                [
                    'name' => 'Dummy Employee ' . $i,
                    'password' => Hash::make('password123'),
                    'role' => 'employee',
                    'device_id' => null,
                    'face_descriptor' => $faceDescriptor,
                    'face_image' => $faceImage,
                    'position' => 'Staff',
                    'basic_salary' => rand(4000000, 6000000),
                    'pph21_status' => 'TK/0',
                ]
                );
            }

            // Collect all employees
            $allEmployees = User::where('employee_role', 'employee')->get();
            $hrAdmin = User::whereIn('employee_role', ['admin', 'hr'])->first();
            $hrAdminId = $hrAdmin ? $hrAdmin->id : null;

            // Generate Data from August 2025 to January 2026
            $startDate = Carbon::create(2025, 8, 1);
            $endDate = Carbon::create(2026, 1, 31);

            $this->info('Generating attendance, leaves, and overtimes from Aug 2025 to Jan 2026...');

            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $isWeekend = $currentDate->isWeekend();

                foreach ($allEmployees as $emp) {
                    // Skip if weekend
                    if ($isWeekend) {
                        continue;
                    }

                    // Random choices
                    // 80% present, 5% leave (cuti), 5% sick (sakit), 5% permit (ijin), 5% absent
                    $chance = rand(1, 100);

                    if ($chance <= 80) {
                        // Present
                        // 20% chance of being late
                        $isLate = rand(1, 100) <= 20;

                        $checkInHour = $isLate ? rand(9, 10) : rand(7, 8);
                        $checkInMin = rand(0, 59);
                        $checkInTime = Carbon::createFromTime($checkInHour, $checkInMin, 0);

                        $checkOutHour = rand(17, 18);
                        $checkOutMin = rand(0, 59);
                        $checkOutTime = Carbon::createFromTime($checkOutHour, $checkOutMin, 0);

                        \App\Models\Presence::create([
                            'employee_id' => $emp->employee_id,
                            'presence_date' => $currentDate->format('Y-m-d'),
                            'presence_time_in' => $checkInTime->format('H:i:s'),
                            'presence_time_out' => $checkOutTime->format('H:i:s'),
                            'presence_status' => $isLate ? 'late' : 'on_time',
                            'presence_photo_in' => 'dummy.png',
                            'presence_photo_out' => 'dummy.png',
                            'presence_lat' => -6.2088,
                            'presence_long' => 106.8456,
                        ]);

                        // 15% chance of overtime if present
                        if (rand(1, 100) <= 15) {
                            $otStart = Carbon::createFromTime(17, 0, 0); // start at 17:00
                            $otHours = rand(1, 4); // 1 to 4 hours overtime
                            $otMinutes = rand(0, 59);
                            $otEnd = $otStart->copy()->addHours($otHours)->addMinutes($otMinutes);

                            $statuses = ['pending', 'approved', 'rejected'];
                            $status = $statuses[array_rand($statuses)];

                            Overtime::create([
                                'user_id' => $emp->id,
                                'date' => $currentDate->format('Y-m-d'),
                                'start_time' => $otStart->format('H:i:s'),
                                'end_time' => $otEnd->format('H:i:s'),
                                'description' => 'Project milestone ' . rand(1, 100),
                                'assigned_by' => $hrAdminId,
                                'status' => $status,
                            ]);
                        }
                    }
                    elseif ($chance <= 95) {
                        // Types: cuti (4 days max normally but we are randomly assigning), sakit, ijin
                        $leaveTypeOptions = ['cuti', 'sakit', 'ijin'];
                        $leaveType = $leaveTypeOptions[array_rand($leaveTypeOptions)];

                        $statuses = ['pending', 'approved', 'rejected'];
                        $status = $statuses[array_rand($statuses)];

                        LeaveRequest::create([
                            'user_id' => $emp->id,
                            'type' => $leaveType,
                            'start_date' => $currentDate->format('Y-m-d'),
                            'end_date' => $currentDate->format('Y-m-d'),
                            'duration' => 1,
                            'reason' => 'Dummy reason for ' . $leaveType,
                            'document_path' => null,
                            'status' => $status,
                        ]);
                    }
                // The remaining 5% is simply absent (no record created)
                }

                $currentDate->addDay();
            }

            // Generate Payrolls for Aug, Sep, Oct, Nov, Dec, Jan
            $this->info('Generating Payrolls...');
            $months = [
                ['month' => 'August', 'year' => 2025, 'start' => '2025-08-01', 'end' => '2025-08-31'],
                ['month' => 'September', 'year' => 2025, 'start' => '2025-09-01', 'end' => '2025-09-30'],
                ['month' => 'October', 'year' => 2025, 'start' => '2025-10-01', 'end' => '2025-10-31'],
                ['month' => 'November', 'year' => 2025, 'start' => '2025-11-01', 'end' => '2025-11-30'],
                ['month' => 'December', 'year' => 2025, 'start' => '2025-12-01', 'end' => '2025-12-31'],
                ['month' => 'January', 'year' => 2026, 'start' => '2026-01-01', 'end' => '2026-01-31'],
            ];

            foreach ($months as $m) {
                foreach ($allEmployees as $emp) {
                    $basic = $emp->basic_salary ?: rand(4000000, 6000000);

                    // Auto calculate overtime for this month
                    $overtimes = Overtime::where('user_id', $emp->id)
                        ->where('status', 'approved')
                        ->whereBetween('date', [$m['start'], $m['end']])
                        ->get();

                    $otPay = 0;
                    foreach ($overtimes as $ot) {
                        $threshold = Carbon::parse($ot->date . ' 17:00:00');
                        $start = Carbon::parse($ot->date . ' ' . $ot->start_time);
                        $end = Carbon::parse($ot->date . ' ' . $ot->end_time);

                        if ($end->lte($threshold))
                            continue;

                        $effectiveStart = $start->greaterThan($threshold) ? $start : $threshold;
                        $diffMinutes = $effectiveStart->diffInMinutes($end);

                        if ($diffMinutes <= 0)
                            continue;

                        $hours = floor($diffMinutes / 60);
                        $remainderMinutes = $diffMinutes % 60;

                        if ($remainderMinutes > 30) {
                            $hours += 1;
                        }

                        $otPay += $hours * 20000;
                    }

                    $thr = $m['month'] == 'December' ? $basic : 0; // Thr on dec
                    $target_bonus = rand(0, 100) > 50 ? 500000 : 0; // chance for target bonus

                    $total_allowances = $thr + $target_bonus + $otPay;
                    $grossMonthly = $basic + $total_allowances;

                    // Fake pph calculation via simplest inline replication
                    $pphStatus = $emp->pph21_status ?? 'TK/0';
                    $ptkp = 54000000;
                    if ($pphStatus === 'K/0')
                        $ptkp = 58500000;
                    elseif ($pphStatus === 'K/1')
                        $ptkp = 63000000;
                    elseif ($pphStatus === 'K/2')
                        $ptkp = 67500000;
                    elseif ($pphStatus === 'K/3')
                        $ptkp = 72000000;

                    $grossYearly = $grossMonthly * 12;
                    $taxable = $grossYearly - $ptkp;

                    $taxYearly = 0;
                    if ($taxable > 0) {
                        if ($taxable <= 60000000) {
                            $taxYearly = $taxable * 0.05;
                        }
                        elseif ($taxable <= 250000000) {
                            $taxYearly = (60000000 * 0.05) + (($taxable - 60000000) * 0.15);
                        }
                        elseif ($taxable <= 500000000) {
                            $taxYearly = (60000000 * 0.05) + (190000000 * 0.15) + (($taxable - 250000000) * 0.25);
                        }
                        else {
                            $taxYearly = (60000000 * 0.05) + (190000000 * 0.15) + (250000000 * 0.25) + (($taxable - 500000000) * 0.30);
                        }
                    }

                    $deductions = round($taxYearly / 12);
                    $net = $grossMonthly - $deductions;

                    Payroll::updateOrCreate([
                        'user_id' => $emp->id,
                        'month' => $m['month'],
                        'year' => $m['year'],
                    ], [
                        'basic_salary' => $basic,
                        'thr' => $thr,
                        'target_bonus' => $target_bonus,
                        'overtime' => $otPay,
                        'allowances' => $total_allowances,
                        'deductions' => $deductions,
                        'net_salary' => $net,
                        'status' => 'pending',
                    ]);
                }
            }

            DB::commit();
            $this->info('Successfully generated all dummy data!');

        }
        catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to generate dummy data: ' . $e->getMessage());
            dump($e->getMessage());
            throw $e;
        }
    }
}
