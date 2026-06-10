<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->employee_role === 'employee') {
            $userId = auth()->id();
            $query = \App\Models\Payroll::with(['employee', 'transactional'])
                ->where('employee_id', $userId)
                ->where('payroll_status', 'approved')
                ->orderBy('payroll_create_at', 'desc');

            $allPayrolls = $query->get();
            $totalRecords = $allPayrolls->count();
            $averageNet = $totalRecords > 0 ? $allPayrolls->avg('payroll_net_salary') : 0;

            // Handle period filter
            $selectedPeriod = $request->get('period', 'all');

            if ($selectedPeriod !== 'all') {
                $parts = explode('-', $selectedPeriod);
                if (count($parts) === 2) {
                    $query->where('payroll_periode_year', $parts[0])->where('payroll_periode_month', $parts[1]);
                }
            }

            $payrolls = $query->get();

            // Calculate Current Stats based on the latest record or filtered record
            $currentGross = 0;
            $currentNet = 0;
            if ($payrolls->count() > 0) {
                // if filtering by 'all', show stats for the most recent month
                $latest = $payrolls->first();
                $currentGross = ($latest->employee->employee_basic_salary ?? 0) + ($latest->transactional ? $latest->transactional->transactional_total : 0);
                $currentNet = $latest->payroll_net_salary;
            }

            // Get unique periods for dropdown
            $periods = \App\Models\Payroll::where('employee_id', $userId)
                ->select('payroll_periode_year as year', 'payroll_periode_month as month')
                ->distinct()
                ->orderBy('payroll_periode_year', 'desc')
                ->orderByRaw("FIELD(payroll_periode_month, 'December', 'November', 'October', 'September', 'August', 'July', 'June', 'May', 'April', 'March', 'February', 'January')")
                ->get();

            return view('payroll.employee', compact('payrolls', 'periods', 'selectedPeriod', 'currentGross', 'currentNet', 'averageNet', 'totalRecords'));
        }

        $payrollGroups = \App\Models\Payroll::select('payroll_periode_month as month', 'payroll_periode_year as year')
            ->selectRaw('count(DISTINCT employee_id) as total_employees')
            ->selectRaw('sum(payroll_net_salary) as total_salary')
            ->groupBy('payroll_periode_month', 'payroll_periode_year')
            ->orderBy('payroll_periode_year', 'desc')
            ->orderByRaw("FIELD(payroll_periode_month, 'December', 'November', 'October', 'September', 'August', 'July', 'June', 'May', 'April', 'March', 'February', 'January')")
            ->get();

        // Owner can generate payroll for both employees AND HR staff
        $rolesToInclude = auth()->user()->employee_role === 'owner' ? ['employee', 'hr'] : ['employee'];
        $employees = \App\Models\Employee::whereIn('employee_role', $rolesToInclude)->get();
        return view('payroll.index', compact('payrollGroups', 'employees'));
    }

    private function calculatePph21($grossMonthly, $status)
    {
        $status = $status ? strtoupper($status) : 'TK/0';
        $ptkp = 54000000;
        if ($status === 'K/0')
            $ptkp = 58500000;
        elseif ($status === 'K/1')
            $ptkp = 63000000;
        elseif ($status === 'K/2')
            $ptkp = 67500000;
        elseif ($status === 'K/3')
            $ptkp = 72000000;

        $grossYearly = $grossMonthly * 12;
        $taxable = $grossYearly - $ptkp;

        if ($taxable <= 0)
            return 0;

        $taxYearly = 0;
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

        return round($taxYearly / 12);
    }
    public function reviewBatch(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employees' => 'required|array',
            'employees.*' => 'exists:employees,employee_id',
            'bonuses' => 'nullable|array',
        ]);

        $monthStr = date('m', strtotime($request->end_date));
        $monthName = date('F', strtotime($request->end_date));
        $year = date('Y', strtotime($request->end_date));
        $periodString = $year . '-' . $monthStr;

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        
        $hke = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$date->isWeekend()) {
                $hke++;
            }
        }

        $calculatedData = [];

        foreach ($request->employees as $empId) {
            $user = \App\Models\Employee::where('employee_id', $empId)->first();
            $basic = $user->employee_basic_salary ?? ($user->basic_salary ?? 0);
            $pphStatus = $user->tax->tax_type ?? 'TK/0';

            $daily_rate = $hke > 0 ? $basic / $hke : 0;

            $thr = $request->bonuses[$empId]['thr'] ?? 0;
            $target_bonus = $request->bonuses[$empId]['target_bonus'] ?? 0;
            
            // Auto Calculate Overtime
            $overtimes = \App\Models\Overtime::where('employee_id', $empId)
                ->where('overtime_status', 'approved')
                ->whereBetween('overtime_date', [$request->start_date, $request->end_date])
                ->get();
            
            $overtimeTotal = 0;
            foreach ($overtimes as $ot) {
                $threshold = \Carbon\Carbon::parse($ot->overtime_date . ' 17:00:00');
                $start = \Carbon\Carbon::parse($ot->overtime_date . ' ' . $ot->overtime_start);
                $end = \Carbon\Carbon::parse($ot->overtime_date . ' ' . $ot->overtime_finish);

                if ($end->lte($threshold)) continue;

                $effectiveStart = $start->greaterThan($threshold) ? $start : $threshold;
                $diffMinutes = $effectiveStart->diffInMinutes($end);

                if ($diffMinutes > 0) {
                    $hours = floor($diffMinutes / 60);
                    $remainderMinutes = $diffMinutes % 60;
                    if ($remainderMinutes > 30) $hours += 1;
                    $overtimeTotal += ($hours * 20000);
                }
            }
            
            $bpjs = $basic * 0.04;
            $transactionalTotal = ($thr + $target_bonus + $overtimeTotal) - $bpjs;

            $reimbursement = \App\Models\Reimbursement::where('employee_id', $empId)
                ->where('reimburse_status', 'approved')
                ->whereBetween('reimburse_date', [$request->start_date, $request->end_date])
                ->sum('reimburse_total');

            $presensis = \App\Models\Presence::where('employee_id', $empId)
                ->whereBetween('presence_date', [$request->start_date, $request->end_date])
                ->get();

            $hadir_count = 0;
            $terlambat_count = 0;

            foreach ($presensis as $p) {
                if ($p->presence_time_in) {
                    $hadir_count++;
                    $timeIn = \Carbon\Carbon::parse($p->presence_time_in);
                    $threshold = \Carbon\Carbon::parse('09:30:00');
                    if ($timeIn->gt($threshold)) {
                        $diffMinutes = $timeIn->diffInMinutes($threshold);
                        if ($diffMinutes > 15) {
                            $terlambat_count++;
                        }
                    }
                }
            }

            $denda_terlambat = $terlambat_count * 50000;
            $grossMonthly = ($hadir_count * $daily_rate) + $transactionalTotal + $reimbursement;
            $tax = $this->calculatePph21($grossMonthly, $pphStatus);
            $net = $grossMonthly - $denda_terlambat - $tax;

            $calculatedData[] = [
                'employee' => $user,
                'basic_salary' => $basic,
                'thr' => $thr,
                'target_bonus' => $target_bonus,
                'overtime' => $overtimeTotal,
                'reimbursement' => $reimbursement,
                'bpjs' => $bpjs,
                'denda_terlambat' => $denda_terlambat,
                'tax' => $tax,
                'gross_monthly' => $grossMonthly,
                'net_salary' => $net,
                'hadir_count' => $hadir_count,
            ];
        }

        // We pass the original request data so it can be submitted from the review page
        $requestData = $request->all();

        return view('payroll.review', compact('calculatedData', 'requestData', 'monthName', 'year', 'hke', 'startDate', 'endDate'));
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employees' => 'required|array',
            'employees.*' => 'exists:employees,employee_id',
            'bonuses' => 'nullable|array',
        ]);

        $monthStr = date('m', strtotime($request->end_date));
        $monthName = date('F', strtotime($request->end_date));
        $year = date('Y', strtotime($request->end_date));
        $periodString = $year . '-' . $monthStr;

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        
        $hke = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$date->isWeekend()) {
                $hke++;
            }
        }

        foreach ($request->employees as $empId) {
            $user = \App\Models\Employee::where('employee_id', $empId)->first();
            $basic = $user->employee_basic_salary ?? ($user->basic_salary ?? 0);
            $pphStatus = $user->tax->tax_type ?? 'TK/0';

            $daily_rate = $hke > 0 ? $basic / $hke : 0;

            $thr = $request->bonuses[$empId]['thr'] ?? 0;
            $target_bonus = $request->bonuses[$empId]['target_bonus'] ?? 0;
            
            // Auto Calculate Overtime
            $overtimes = \App\Models\Overtime::where('employee_id', $empId)
                ->where('overtime_status', 'approved')
                ->whereBetween('overtime_date', [$request->start_date, $request->end_date])
                ->get();
            
            $overtimeTotal = 0;
            foreach ($overtimes as $ot) {
                $threshold = \Carbon\Carbon::parse($ot->overtime_date . ' 17:00:00');
                $start = \Carbon\Carbon::parse($ot->overtime_date . ' ' . $ot->overtime_start);
                $end = \Carbon\Carbon::parse($ot->overtime_date . ' ' . $ot->overtime_finish);

                if ($end->lte($threshold)) continue;

                $effectiveStart = $start->greaterThan($threshold) ? $start : $threshold;
                $diffMinutes = $effectiveStart->diffInMinutes($end);

                if ($diffMinutes > 0) {
                    $hours = floor($diffMinutes / 60);
                    $remainderMinutes = $diffMinutes % 60;
                    if ($remainderMinutes > 30) $hours += 1;
                    $overtimeTotal += ($hours * 20000);
                }
            }
            
            $bpjs = $basic * 0.04;
            $transactionalTotal = ($thr + $target_bonus + $overtimeTotal) - $bpjs;

            $transactional = \App\Models\Transactional::updateOrCreate([
                'employee_id' => $empId,
                'transactional_month' => $periodString,
            ], [
                'transactional_thr' => $thr,
                'transactional_bonus' => $target_bonus,
                'transactional_overtime' => $overtimeTotal,
                'transactional_bpjs' => $bpjs,
                'transactional_total' => $transactionalTotal,
            ]);

            $reimbursement = \App\Models\Reimbursement::where('employee_id', $empId)
                ->where('reimburse_status', 'approved')
                ->whereBetween('reimburse_date', [$request->start_date, $request->end_date])
                ->sum('reimburse_total');

            $presensis = \App\Models\Presence::where('employee_id', $empId)
                ->whereBetween('presence_date', [$request->start_date, $request->end_date])
                ->get();

            $hadir_count = 0;
            $terlambat_count = 0;

            foreach ($presensis as $p) {
                if ($p->presence_time_in) {
                    $hadir_count++;
                    $timeIn = \Carbon\Carbon::parse($p->presence_time_in);
                    $threshold = \Carbon\Carbon::parse('09:30:00');
                    if ($timeIn->gt($threshold)) {
                        $diffMinutes = $timeIn->diffInMinutes($threshold);
                        if ($diffMinutes > 15) {
                            $terlambat_count++;
                        }
                    }
                }
            }

            $denda_terlambat = $terlambat_count * 50000;
            $grossMonthly = ($hadir_count * $daily_rate) + $transactionalTotal + $reimbursement;
            $tax = $this->calculatePph21($grossMonthly, $pphStatus);
            $net = $grossMonthly - $denda_terlambat - $tax;

            // Generate PY01 format
            $latestPayroll = \App\Models\Payroll::orderBy('payroll_id', 'desc')->first();
            $nextId = 'PY01';
            if ($latestPayroll) {
                $num = preg_replace("/[^0-9\.]/", '', $latestPayroll->payroll_id);
                $nextId = 'PY' . sprintf('%02d', $num + 1);
            }

            \App\Models\Payroll::create([
                'payroll_id' => $nextId,
                'employee_id' => $empId,
                'transactional_id' => $transactional->transactional_id,
                'payroll_periode_month' => $monthName,
                'payroll_periode_year' => $year,
                'payroll_reimburse_total' => $reimbursement,
                'payroll_total_attendance' => $hadir_count,
                'payroll_total_late' => $denda_terlambat,
                'payroll_tax' => $tax,
                'payroll_net_salary' => $net,
                'payroll_status' => 'pending',
            ]);
        }

        return redirect()->route('payroll.index')->with('success', 'Payroll successfully generated for ' . $monthName . ' ' . $year);
    }

    public function generatePDF($month, $year, Request $request)
    {
        $query = \App\Models\Payroll::with(['employee', 'transactional'])
            ->where('payroll_periode_month', $month)
            ->where('payroll_periode_year', $year);
            
        if (auth()->user()->employee_role === 'employee') {
            $query->where('employee_id', auth()->id())
                  ->where('payroll_status', 'approved');
        } elseif ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $payrolls = $query->get();

        if ($payrolls->isEmpty()) {
            return back()->with('error', 'No payroll records found or not yet approved.');
        }

        $startDate = \Carbon\Carbon::parse("1 $month $year");
        $endDate = $startDate->copy()->endOfMonth();
        
        $hke = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$date->isWeekend()) {
                $hke++;
            }
        }

        foreach ($payrolls as $payroll) {
            $payroll->hke = $hke;
            $basic = $payroll->employee->employee_basic_salary ?? 0;
            $payroll->daily_rate = $hke > 0 ? $basic / $hke : 0;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.pdf', compact('payrolls', 'month', 'year'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Payroll_Slip_' . $month . '_' . $year . '.pdf');
    }

    public function generateReportPDF($month, $year)
    {
        $payrolls = \App\Models\Payroll::with('employee')
            ->where('payroll_periode_month', $month)
            ->where('payroll_periode_year', $year)
            ->get();

        if ($payrolls->isEmpty()) {
            return back()->with('error', 'No payroll records found for this period.');
        }

        $totalEmployees = $payrolls->count();
        $totalGross = $payrolls->sum(function ($p) {
            return ($p->employee->basic_salary ?? 0) + ($p->transactional ? $p->transactional->transactional_total : 0); 
        });
        $totalDeductions = $payrolls->sum(function ($p) {
            return $p->payroll_tax + $p->payroll_total_late;
        });
        $totalNet = $payrolls->sum('payroll_net_salary');
        $pendingApprovals = $payrolls->where('payroll_status', 'pending')->count();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.report_pdf', compact(
            'month', 'year', 'payrolls', 'totalEmployees', 'totalGross', 'totalDeductions', 'totalNet', 'pendingApprovals'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Payroll_Report_' . $month . '_' . $year . '.pdf');
    }

    public function report($month, $year)
    {
        $payrolls = \App\Models\Payroll::with('employee')
            ->where('payroll_periode_month', $month)
            ->where('payroll_periode_year', $year)
            ->get();

        $startDate = \Carbon\Carbon::parse("1 $month $year");
        $endDate = $startDate->copy()->endOfMonth();
        
        $hke = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if (!$date->isWeekend()) {
                $hke++;
            }
        }

        $totalEmployees = $payrolls->count();
        $totalGross = $payrolls->sum(function ($p) use ($hke) {
            $basic = $p->employee->employee_basic_salary ?? ($p->employee->basic_salary ?? 0);
            $daily = $hke > 0 ? $basic / $hke : 0;
            $hadir = $p->payroll_total_attendance ?? 0;
            $trans = $p->transactional ? $p->transactional->transactional_total : 0;
            $reimb = $p->payroll_reimburse_total ?? 0;
            return ($hadir * $daily) + $trans + $reimb;
        });

        $totalDeductions = $payrolls->sum(function ($p) {
            return $p->payroll_tax + $p->payroll_total_late;
        });
        $totalNet = $payrolls->sum('payroll_net_salary');
        $pendingApprovals = $payrolls->where('payroll_status', 'pending')->count();

        $totalAttendanceDeduction = $payrolls->sum(function ($p) {
            return $p->payroll_total_late;
        });
        $totalTaxDeduction = $payrolls->sum('payroll_tax');
        $totalBpjsDeduction = $payrolls->sum(function ($p) { return $p->transactional ? $p->transactional->transactional_bpjs : 0; });

        $allPayrolls = $payrolls;
        $pendingPayrolls = $payrolls->where('payroll_status', 'pending');
        $approvedPayrolls = $payrolls->where('payroll_status', 'approved');
        $rejectedPayrolls = $payrolls->where('payroll_status', 'rejected');

        $totalHadir = $payrolls->sum('payroll_total_attendance');
        $expectedTotalHadir = $totalEmployees * $hke;
        $avgAttendanceRate = $expectedTotalHadir > 0 ? ($totalHadir / $expectedTotalHadir) * 100 : 0;

        return view('payroll.report', compact(
            'month', 'year', 'payrolls', 'hke',
            'totalEmployees', 'totalGross', 'totalDeductions', 'totalNet', 'pendingApprovals', 'avgAttendanceRate',
            'allPayrolls', 'pendingPayrolls', 'approvedPayrolls', 'rejectedPayrolls',
            'totalAttendanceDeduction', 'totalTaxDeduction', 'totalBpjsDeduction'
        ));
    }

    public function approveBatch(Request $request)
    {
        $request->validate([
            'payroll_ids' => 'required|array',
            'payroll_ids.*' => 'exists:payrolls,payroll_id',
            'action' => 'required|in:approve,reject'
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        \App\Models\Payroll::whereIn('payroll_id', $request->payroll_ids)->update(['payroll_status' => $status]);

        return back()->with('success', 'Payroll statuses successfully ' . $status . '.');
    }

    public function getAutoOvertime(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!$startDate || !$endDate) {
            return response()->json(['error' => 'Start date and end date are required'], 400);
        }

        $overtimes = \App\Models\Overtime::where('overtime_status', 'approved')
            ->whereBetween('overtime_date', [$startDate, $endDate])
            ->get();

        $employeeOvertimes = [];

        foreach ($overtimes as $ot) {
            $userId = $ot->employee_id;

            // Base threshold is 17:00:00
            $threshold = \Carbon\Carbon::parse($ot->overtime_date . ' 17:00:00');
            $start = \Carbon\Carbon::parse($ot->overtime_date . ' ' . $ot->overtime_start);
            $end = \Carbon\Carbon::parse($ot->overtime_date . ' ' . $ot->overtime_finish);

            // If the overtime ended before 17:00, it's not counted
            if ($end->lte($threshold)) {
                continue;
            }

            // The effective start time is the latter of the actual start time or 17:00
            $effectiveStart = $start->greaterThan($threshold) ? $start : $threshold;

            $diffMinutes = $effectiveStart->diffInMinutes($end);

            if ($diffMinutes <= 0) {
                continue;
            }

            // Calculate hours based on rules
            $hours = floor($diffMinutes / 60);
            $remainderMinutes = $diffMinutes % 60;

            if ($remainderMinutes > 30) {
                $hours += 1; // Round up to 1 hour
            }

            $pay = $hours * 20000;

            if (!isset($employeeOvertimes[$userId])) {
                $employeeOvertimes[$userId] = 0;
            }

            $employeeOvertimes[$userId] += $pay;
        }

        return response()->json($employeeOvertimes);
    }
}